<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Announcement types
    const TYPE_ANNOUNCEMENT = 'announcement';
    const TYPE_SYSTEM = 'system';
    const TYPE_QUIZ_SUBMITTED = 'quiz_submitted';
    const TYPE_NEW_STUDENT = 'new_student';
    const TYPE_DEADLINE_APPROACHING = 'deadline_approaching';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope to get only announcements
    public function scopeAnnouncements($query)
    {
        return $query->where('type', self::TYPE_ANNOUNCEMENT);
    }

    // Scope to get unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}