<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogsController extends Controller
{
    use ApiResponseTrait;

    /**
     * جميع سجلات التدقيق للمستخدم الحالي مع فلترة
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'event_type' => 'nullable|string|in:Login,Logout,StatusChange,CallStarted,CallEnded,MessageDeleted,FileUploaded,ProfileUpdated,PasswordChanged,Other',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'ip' => 'nullable|string',
            'device_info' => 'nullable|string',
        ]);

        $logs = AuditLog::where('user_id', $request->user()->id)
            ->when($validated['event_type'] ?? null, fn($q, $type) => $q->where('event_type', $type))
            ->when($validated['from'] ?? null, fn($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($validated['ip'] ?? null, fn($q, $ip) => $q->where('ip_address', 'LIKE', "%$ip%"))
            ->when($validated['device_info'] ?? null, fn($q, $device) => $q->where('device_info', 'LIKE', "%$device%"))
            ->latest()
            ->paginate(20);

        return $this->success($logs, 'سجل الأحداث الخاص بك');
    }

    /**
     * إنشاء سجل تدقيق جديد
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'event_type' => 'required|string|in:Login,Logout,StatusChange,CallStarted,CallEnded,MessageDeleted,FileUploaded,ProfileUpdated,PasswordChanged,Other',
                'event_details' => 'nullable|string',
            ]);

            $log = AuditLog::create([
                'user_id' => $request->user()->id,
                'event_type' => $validated['event_type'],
                'event_details' => $validated['event_details'] ?? null,
                'device_info' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
            ]);

            return $this->success($log, 'تم إنشاء سجل التدقيق', 201);
        });
    }

    /**
     * تفاصيل سجل تدقيق واحد
     */
    public function show(Request $request, $id)
    {
        $log = AuditLog::where('user_id', $request->user()->id)->find($id);

        if (!$log) {
            return $this->notFound('سجل التدقيق غير موجود');
        }

        return $this->success($log, 'تفاصيل سجل التدقيق');
    }
}
