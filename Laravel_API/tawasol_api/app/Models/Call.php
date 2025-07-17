<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'caller_id',
        'receiver_id',
        'call_type',
        'status',
        'started_at',
        'ended_at',
        'duration_seconds',
        'created_at',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
        'created_at',
    ];

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
