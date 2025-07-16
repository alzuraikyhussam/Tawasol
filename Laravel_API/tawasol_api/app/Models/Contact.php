<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    public $timestamps = false; // فقط created_at غير معتمد تلقائياً في Laravel في هذا الجدول

    protected $fillable = [
        'owner_id',
        'contact_user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // صاحب قائمة جهات الاتصال
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // المستخدم الذي هو جهة اتصال
    public function contactUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }
}
