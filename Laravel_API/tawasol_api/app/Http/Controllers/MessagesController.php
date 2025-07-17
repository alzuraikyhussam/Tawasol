<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\File;
use App\Models\Message;
use App\Models\MessageStatus;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessagesController extends Controller
{
    use ApiResponseTrait;

    /**
     * جلب كل الرسائل لشات معيّن (مع Pagination)
     */
    public function index(Request $request, $chatId)
    {
        $chat = Chat::find($chatId);

        if (!$chat) {
            return $this->notFound('المحادثة غير موجودة');
        }

        $messages = Message::where('chat_id', $chatId)
            ->with(['sender', 'messageType', 'file'])
            ->latest()
            ->paginate(20);

        return $this->success($messages, 'قائمة الرسائل');
    }

    /**
     * إرسال رسالة جديدة مع رفع ملف وتخزينه في جدول files وربطها مع الرسالة
     */
    public function store(Request $request, $chatId)
    {
        return DB::transaction(function () use ($request, $chatId) {

            $chat = Chat::find($chatId);

            if (!$chat) {
                return $this->notFound('المحادثة غير موجودة');
            }

            $validated = $request->validate([
                'message_type_id' => 'required|exists:message_types,id',
                'content' => 'nullable|string',
                'file' => 'nullable|file|mimes:jpeg,jpg,png,gif,mp4,mkv,mov,mp3,wav,pdf,doc,docx|max:20480', // 20MB
            ]);

            $fileRecord = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();

                if (Str::startsWith($mimeType, 'image/')) {
                    $folder = 'messages/images/';
                    $prefix = 'image_';
                } elseif (Str::startsWith($mimeType, 'video/')) {
                    $folder = 'messages/videos/';
                    $prefix = 'video_';
                } elseif (Str::startsWith($mimeType, 'audio/')) {
                    $folder = 'messages/voices/';
                    $prefix = 'voice_';
                } elseif (in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx'])) {
                    $folder = 'messages/docs/';
                    $prefix = 'doc_';
                } else {
                    $folder = 'messages/others/';
                    $prefix = 'file_';
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $prefix . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;

                $path = $file->storeAs($folder, $fileName, 'public');

                $fileRecord = File::create([
                    'uploaded_by' => $request->user()->id,
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'uploaded_at' => now(),
                ]);
            }

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $request->user()->id,
                'message_type_id' => $validated['message_type_id'],
                'content' => $validated['content'] ?? null,
                'file_url' => $fileRecord ? asset('storage/' . $fileRecord->file_path) : null,
                'file_id' => $fileRecord ? $fileRecord->id : null,
            ]);

            // إضافة حالة الرسالة لكل الأعضاء
            foreach ($chat->members as $member) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'user_id' => $member->user_id,
                    'status' => $member->user_id == $request->user()->id ? 'Read' : 'Sent',
                ]);
            }

            AuditLogger::log(
                $request->user()->id,
                'FileUploaded',
                'تم رفع الملف: ' . $fileName
            );

            return $this->success($message->load(['sender', 'messageType', 'file']), 'تم إرسال الرسالة بنجاح', 201);

        });
    }

    /**
     * تفاصيل رسالة واحدة مع الملف المرتبط
     */
    public function show($id)
    {
        $message = Message::with(['sender', 'messageType', 'chat', 'file'])->find($id);

        if (!$message) {
            return $this->notFound('الرسالة غير موجودة');
        }

        return $this->success($message, 'تفاصيل الرسالة');
    }

    /**
     * حذف رسالة مع حذف ملفها من التخزين وقاعدة البيانات
     */
    public function destroy(Request $request, $id)
    {
        $message = Message::with('file')->find($id);

        if (!$message) {
            return $this->notFound('الرسالة غير موجودة');
        }

        if ($message->sender_id != $request->user()->id) {
            return $this->error('غير مصرح لك بحذف هذه الرسالة', 403);
        }

        return DB::transaction(function () use ($message) {
            // حذف الملف المرتبط
            if ($message->file) {
                if (Storage::disk('public')->exists($message->file->file_path)) {
                    Storage::disk('public')->delete($message->file->file_path);
                }
                $message->file->delete();
            }

            // حذف الرسالة
            $message->delete();

            return $this->success(null, 'تم حذف الرسالة بنجاح');

        });

        AuditLogger::log(
            $request->user()->id,
            'MessageDeleted',
            'تم حذف رسالة ID: ' . $message->id
        );
    }

    /**
     * تعليم رسالة كمقروءة
     */
    public function markAsRead(Request $request, $id)
    {
        $status = MessageStatus::where('message_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$status) {
            return $this->notFound('لا يوجد سجل حالة لهذه الرسالة');
        }

        $status->update(['status' => 'Read']);

        return $this->success(null, 'تم تعليم الرسالة كمقروءة');
    }

    /**
     * تعليم جميع رسائل شات كمقروءة
     */
    public function bulkMarkAsRead(Request $request, $chatId)
    {
        MessageStatus::whereHas('message', function ($q) use ($chatId) {
            $q->where('chat_id', $chatId);
        })->where('user_id', $request->user()->id)
            ->update(['status' => 'Read']);

        return $this->success(null, 'تم تعليم جميع الرسائل كمقروءة');
    }

    /**
     * البحث في الرسائل
     */
    public function search(Request $request, $chatId)
    {
        $validated = $request->validate([
            'q' => 'required|string',
        ]);

        $messages = Message::where('chat_id', $chatId)
            ->where('content', 'LIKE', '%' . $validated['q'] . '%')
            ->with(['sender', 'messageType'])
            ->latest()
            ->paginate(20);

        return $this->success($messages, 'نتائج البحث');
    }
}
