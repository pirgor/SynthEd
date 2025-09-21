<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizUpload extends Model
{
    use HasFactory;
    protected $fillable = ['quiz_id', 'filename', 'filepath'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
