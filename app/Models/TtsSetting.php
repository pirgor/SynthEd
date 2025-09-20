<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TtsSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'voice_id',
        'speed',
        'stability',
        'similarity_boost',
    ];
}
