<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function uploads()
    {
        return $this->hasMany(LessonUpload::class);
    }
}
