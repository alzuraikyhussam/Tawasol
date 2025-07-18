<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    use SerializeDate;

    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time',
        'ip_address',
        'device_info',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
