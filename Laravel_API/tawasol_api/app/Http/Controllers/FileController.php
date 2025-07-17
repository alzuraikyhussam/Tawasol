<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\AuditLogger;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    use ApiResponseTrait;

    /**
     * عرض قائمة الملفات مع الفلترة والبحث
     */
    public function index(Request $request)
    {
        $query = File::query()->with('uploadedBy:id,full_name,username');

        if ($request->has('uploaded_by')) {
            $query->where('uploaded_by', $request->input('uploaded_by'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('file_name', 'LIKE', "%$search%");
        }

        $files = $query->latest()->paginate(20);

        return $this->success($files, 'قائمة الملفات');
    }

    /**
     * رفع ملف وحفظه في جدول الملفات
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,mkv,mov,mp3,wav,pdf,doc,docx,xlsx,xls,csv,txt|max:51200', // 50MB
        ]);

        return DB::transaction(function () use ($request) {

            $file = $request->file('file');
            $mimeType = $file->getMimeType();

            if (Str::startsWith($mimeType, 'image/')) {
                $folder = 'files/images/';
                $prefix = 'image_';
            } elseif (Str::startsWith($mimeType, 'video/')) {
                $folder = 'files/videos/';
                $prefix = 'video_';
            } elseif (Str::startsWith($mimeType, 'audio/')) {
                $folder = 'files/audios/';
                $prefix = 'audio_';
            } elseif (in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx', 'xlsx', 'xls', 'csv', 'txt'])) {
                $folder = 'files/docs/';
                $prefix = 'doc_';
            } else {
                $folder = 'files/others/';
                $prefix = 'file_';
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = $prefix . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;

            $path = $file->storeAs($folder, $fileName, 'public');

            $savedFile = File::create([
                'uploaded_by' => $request->user()->id,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
            ]);

            AuditLogger::log(
                $request->user()->id,
                'FileUploaded',
                'تم رفع الملف: ' . $fileName
            );

            return $this->success($savedFile, 'تم رفع الملف بنجاح', 201);

        });
    }

    /**
     * حذف ملف من التخزين وقاعدة البيانات
     */
    public function destroy(Request $request, $id)
    {
        $file = File::find($id);

        if (!$file) {
            return $this->notFound('الملف غير موجود');
        }

        // يمكن اضافة تحقق صلاحيات حذف هنا حسب الحاجة

        return DB::transaction(function () use ($file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();

            return $this->success(null, 'تم حذف الملف بنجاح');
        });
    }

    /**
     * عرض تفاصيل ملف
     */
    public function show($id)
    {
        $file = File::with('uploadedBy:id,full_name,username')->find($id);

        if (!$file) {
            return $this->notFound('الملف غير موجود');
        }

        return $this->success($file, 'تفاصيل الملف');
    }
}
