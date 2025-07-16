<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageStatus;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->with(['sender', 'messageType'])
            ->latest()
            ->paginate(20);

        return $this->success($messages, 'قائمة الرسائل');
    }

    /**
     * إرسال رسالة جديدة
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
                'file_url' => 'nullable|string',
            ]);

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_id' => $request->user()->id,
                'message_type_id' => $validated['message_type_id'],
                'content' => $validated['content'] ?? null,
                'file_url' => $validated['file_url'] ?? null,
            ]);

            // إضافة حالة الرسالة لكل الأعضاء
            foreach ($chat->members as $member) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'user_id' => $member->user_id,
                    'status' => $member->user_id == $request->user()->id ? 'Read' : 'Sent',
                ]);
            }

            return $this->success($message->load(['sender', 'messageType']), 'تم إرسال الرسالة', 201);
        });
    }

    /**
     * تفاصيل رسالة واحدة
     */
    public function show($id)
    {
        $message = Message::with(['sender', 'messageType', 'chat'])->find($id);

        if (!$message) {
            return $this->notFound('الرسالة غير موجودة');
        }

        return $this->success($message, 'تفاصيل الرسالة');
    }

    /**
     * حذف رسالة
     */
    public function destroy(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return $this->notFound('الرسالة غير موجودة');
        }

        // صلاحية: فقط المرسل يمكنه الحذف
        if ($message->sender_id != $request->user()->id) {
            return $this->error('غير مصرح لك بحذف هذه الرسالة', 403);
        }

        $message->delete();

        return $this->success(null, 'تم حذف الرسالة');
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
     * تعليم كل رسائل شات معيّن كمقروءة
     */
    public function bulkMarkAsRead(Request $request, $chatId)
    {
        $statuses = MessageStatus::whereHas('message', function ($q) use ($chatId) {
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
