<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    public $timestamps = false; // يحتوي فقط على created_at وهو مخصص

    protected $fillable = [
        'department_id',
        'is_group',
        'group_name',
        'group_description',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // القسم المرتبط بالمحادثة
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // منشئ المحادثة
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // أعضاء المحادثة
    public function members(): HasMany
    {
        return $this->hasMany(ChatMember::class);
    }

    // الرسائل داخل المحادثة
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
