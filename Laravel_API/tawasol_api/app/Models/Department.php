<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    public $timestamps = false; // لأنه لا يحتوي سوى created_at

    protected $fillable = ['name'];

    // علاقة 1-to-many مع المستخدمين
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // علاقة 1-to-many مع المحادثات
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
