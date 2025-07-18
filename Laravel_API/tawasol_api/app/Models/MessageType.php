<?php

namespace App\Models;

use App\Traits\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageType extends Model
{
    use SerializeDate;
    
    protected $fillable = ['type'];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
