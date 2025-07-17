<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginLogsController extends Controller
{
    use ApiResponseTrait;

    /**
     * جميع سجلات الدخول للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $logs = LoginLog::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return $this->success($logs, 'سجل الدخول الخاص بك');
    }

    /**
     * إنشاء سجل دخول جديد (عند تسجيل الدخول)
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $log = LoginLog::create([
                'user_id' => $request->user()->id,
                'login_time' => now(),
                'ip_address' => $request->ip(),
                'device_info' => $request->header('User-Agent'),
            ]);

            return $this->success($log, 'تم إنشاء سجل الدخول', 201);
        });
    }

    /**
     * تسجيل الخروج وتحديث سجل الخروج
     */
    public function logout(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $log = LoginLog::where('user_id', $request->user()->id)
                ->whereNull('logout_time')
                ->latest()
                ->first();

            if (!$log) {
                return $this->error('لا يوجد سجل دخول نشط', 404);
            }

            $log->update(['logout_time' => now()]);

            return $this->success(null, 'تم تحديث وقت الخروج بنجاح');
        });
    }

    /**
     * سجل الدخول مع فلترة اختيارية
     */
    public function history(Request $request)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'ip' => 'nullable|string',
            'device_info' => 'nullable|string',
        ]);

        $logs = LoginLog::where('user_id', $request->user()->id)
            ->when($validated['from'] ?? null, fn($q, $from) => $q->whereDate('login_time', '>=', $from))
            ->when($validated['to'] ?? null, fn($q, $to) => $q->whereDate('login_time', '<=', $to))
            ->when($validated['ip'] ?? null, fn($q, $ip) => $q->where('ip_address', 'LIKE', "%$ip%"))
            ->when($validated['device_info'] ?? null, fn($q, $device) => $q->where('device_info', 'LIKE', "%$device%"))
            ->latest()
            ->paginate(20);

        return $this->success($logs, 'سجل الدخول المفصّل');
    }
}
