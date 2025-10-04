<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $instructor = User::where('user_role', 'instructor')->first();

        if ($instructor) {
            // In database/seeders/NotificationSeeder.php
            Notification::create([
                'user_id' => $instructor->id,
                'type' => 'quiz_submitted',
                'title' => 'Quiz Submitted',
                'message' => 'John Doe has submitted the "Introduction to Biology" quiz',
                'is_read' => false,  // Changed from 'read'
                'data' => [
                    'student_name' => 'John Doe',
                    'quiz_title' => 'Introduction to Biology',
                    'student_id' => 1,
                    'quiz_id' => 1
                ]
            ]);

            Notification::create([
                'user_id' => $instructor->id,
                'type' => 'new_student',
                'title' => 'New Student Enrolled',
                'message' => 'Jane Smith has enrolled in your Chemistry course',
                'is_read' => false,  // Changed from 'read'
                'data' => [
                    'student_name' => 'Jane Smith',
                    'course_name' => 'Chemistry',
                    'student_id' => 2
                ]
            ]);

            Notification::create([
                'user_id' => $instructor->id,
                'type' => 'deadline_approaching',
                'title' => 'Quiz Deadline Approaching',
                'message' => 'The "Physics Midterm" quiz deadline is in 2 days',
                'is_read' => true,  // Changed from 'read'
                'read_at' => now()->subHours(3),
                'data' => [
                    'quiz_title' => 'Physics Midterm',
                    'deadline' => now()->addDays(2),
                    'quiz_id' => 2
                ]
            ]);
        }
    }
}
