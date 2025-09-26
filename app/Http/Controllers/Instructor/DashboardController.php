<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get instructor's quizzes
        $quizzes = Quiz::all(); // You might want to filter by instructor if you have that relationship

        // Get students count
        $studentsCount = User::where('user_role', 'student')->count();

        // Get recent quiz attempts
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get quiz statistics
        $quizStats = [
            'total' => $quizzes->count(),
            'active' => $quizzes->where('deadline', '>', now())->count(),
            'expired' => $quizzes->where('deadline', '<=', now())->count(),
        ];

        return view('instructor.dashboard', compact(
            'quizzes',
            'studentsCount',
            'recentAttempts',
            'quizStats'
        ));
    }
}
