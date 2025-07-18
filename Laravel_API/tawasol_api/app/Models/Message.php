<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use SerializeDate;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'message_type_id',
        'content',
        'file_id',
        'file_url',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function messageType(): BelongsTo
    {
        return $this->belongsTo(MessageType::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    // حالة الرسائل المتعددة
    public function statuses(): HasMany
    {
        return $this->hasMany(MessageStatus::class);
    }
}
