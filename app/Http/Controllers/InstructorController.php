<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalQuizzes' => Quiz::count(),
            'totalStudents' => User::where('user_role', 'student')->count(),
            'totalAttempts' => QuizAttempt::count(),
            'recentAttempts' => QuizAttempt::with('user', 'quiz')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];
        
        return view('instructor.dashboard', compact('stats'));
    }
}