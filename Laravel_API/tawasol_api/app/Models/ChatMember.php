<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMember extends Model
{
    use SerializeDate;
    
    protected $fillable = [
        'chat_id',
        'user_id',
        'is_admin',
        'is_muted',
        'joined_at',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_muted' => 'boolean',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
