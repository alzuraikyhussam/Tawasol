<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallsController extends Controller
{
    use ApiResponseTrait;

    /**
     * جميع مكالمات المستخدم
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $calls = Call::where('caller_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['caller', 'receiver'])
            ->latest()
            ->paginate(20);

        return $this->success($calls, 'قائمة المكالمات');
    }

    /**
     * بدء مكالمة جديدة
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'call_type' => 'required|in:Voice,Video',
            ]);

            $call = Call::create([
                'caller_id' => $request->user()->id,
                'receiver_id' => $validated['receiver_id'],
                'call_type' => $validated['call_type'],
                'status' => 'Ringing',
                'created_at' => now(),
            ]);

            return $this->success($call->load(['caller', 'receiver']), 'تم بدء المكالمة', 201);
        });
    }

    /**
     * تحديث حالة المكالمة (مثلاً: إكمال أو إلغاء)
     */
    public function updateStatus(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $call = Call::find($id);

            if (!$call) {
                return $this->notFound('المكالمة غير موجودة');
            }

            $validated = $request->validate([
                'status' => 'required|in:Ringing,Missed,Completed,Declined',
                'started_at' => 'nullable|date',
                'ended_at' => 'nullable|date',
                'duration_seconds' => 'nullable|integer|min:0',
            ]);

            $call->update($validated);

            return $this->success($call, 'تم تحديث حالة المكالمة');
        });
    }

    /**
     * تفاصيل مكالمة واحدة
     */
    public function show($id)
    {
        $call = Call::with(['caller', 'receiver'])->find($id);

        if (!$call) {
            return $this->notFound('المكالمة غير موجودة');
        }

        return $this->success($call, 'تفاصيل المكالمة');
    }

    /**
     * سجل المكالمات مع فلترة اختياريّة
     */
    public function history(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'status' => 'nullable|in:Ringing,Missed,Completed,Declined',
            'call_type' => 'nullable|in:Voice,Video',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $calls = Call::where(function ($q) use ($userId) {
            $q->where('caller_id', $userId)
                ->orWhere('receiver_id', $userId);
        })
            ->when($validated['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($validated['call_type'] ?? null, fn($q, $type) => $q->where('call_type', $type))
            ->when($validated['from'] ?? null, fn($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->with(['caller', 'receiver'])
            ->latest()
            ->paginate(20);

        return $this->success($calls, 'سجل المكالمات');
    }
}
