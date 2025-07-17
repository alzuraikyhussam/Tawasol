<?php

namespace App\Http\Controllers;

use App\Models\MessageStatus;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class MessageStatusController extends Controller
{
    use ApiResponseTrait;

    /**
     * جلب كل الحالات لرسالة معيّنة
     */
    public function index($messageId)
    {
        $statuses = MessageStatus::with('user')
            ->where('message_id', $messageId)
            ->get();

        return $this->success($statuses, 'حالات الرسالة');
    }

    /**
     * تحديث حالة رسالة واحدة للمستخدم الحالي
     */
    public function update(Request $request, $messageId)
    {
        $status = MessageStatus::where('message_id', $messageId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$status) {
            return $this->notFound('حالة الرسالة غير موجودة');
        }

        $validated = $request->validate([
            'status' => 'required|in:Sent,Delivered,Read',
        ]);

        $status->update(['status' => $validated['status']]);

        return $this->success($status, 'تم تحديث الحالة');
    }

    /**
     * تحديث حالات رسائل كثيرة كمقروءة
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        MessageStatus::whereIn('message_id', $validated['message_ids'])
            ->where('user_id', $request->user()->id)
            ->update(['status' => 'Read']);

        return $this->success(null, 'تم تعليم الرسائل كمقروءة');
    }
}
