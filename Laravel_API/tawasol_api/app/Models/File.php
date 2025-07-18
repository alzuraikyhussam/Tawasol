<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use SerializeDate;

    protected $fillable = [
        'uploaded_by',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_at',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function messages()
    {
        // في حال تم ربط ملفات برسائل متعددة (عادة ملف واحد مرتبط برسالة واحدة فقط)
        return $this->hasMany(Message::class, 'file_id');
    }
}
