<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageType extends Model
{
    public $timestamps = false;

    protected $fillable = ['type'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
