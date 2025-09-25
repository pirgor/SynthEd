<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonUpload extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'file_path', 'file_name', 'file_type', 'extracted_text'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
