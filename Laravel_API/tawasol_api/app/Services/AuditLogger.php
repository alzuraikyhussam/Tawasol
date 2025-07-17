<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public static function log($userId, $eventType, $eventDetails = null, $ipAddress = null, $deviceInfo = null)
    {
        /** @var Request $request */
        $request = request();

        return AuditLog::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'event_details' => $eventDetails,
            'ip_address' => $ipAddress ?? $request->ip(),
            'device_info' => $deviceInfo ?? $request->header('User-Agent'),
        ]);
    }
}
