<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_message',
        'bot_reply',
    ];

    // 🔗 Each message belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
