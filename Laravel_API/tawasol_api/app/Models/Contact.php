<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use SerializeDate;
    
    protected $fillable = [
        'owner_id',
        'contact_user_id',
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
