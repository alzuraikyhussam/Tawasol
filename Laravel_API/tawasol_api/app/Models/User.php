<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
>>>>>>> e1ba519c8197441cbae35fb4f353df82f8acba96

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'department_id',
        'username',
        'full_name',
        'password_hash',
        'is_active',
        'is_online',
        'last_seen',
        'profile_image_url',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
    ];

    protected $dates = [
        'last_seen',
        'created_at',
    ];

    // علاقة المستخدم بقسمه
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // علاقات أخرى:

    // جهات الاتصال التي يمتلكها المستخدم

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'owner_id');
    }

    public function contactOf()
    {
        return $this->hasMany(Contact::class, 'contact_user_id');
    }

    // جهات الاتصال التي هي بمثابة مستخدم جهة اتصال
    public function contactedBy()
    {
        return $this->hasMany(Contact::class, 'contact_user_id');
    }

    // المحادثات التي أنشأها المستخدم
    public function createdChats()
    {
        return $this->hasMany(Chat::class, 'created_by');
    }

    // العضويات في المحادثات
    public function chatMemberships()
    {
        return $this->hasMany(ChatMember::class);
    }

    // الرسائل المرسلة
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // الملفات التي رفعها
    public function uploadedFiles()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    // سجلات الدخول
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    // إعدادات المستخدم
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    // المكالمات كمتصل
    public function callsMade()
    {
        return $this->hasMany(Call::class, 'caller_id');
    }

    // المكالمات كمتصل عليه
    public function callsReceived()
    {
        return $this->hasMany(Call::class, 'receiver_id');
    }

    // سجلات الأحداث
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class);
    }
}
