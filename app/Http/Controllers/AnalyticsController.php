<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalQuizzes = Quiz::count();
        $totalStudents = User::where('user_role', 'student')->count();
        $totalAttempts = QuizAttempt::count();
        
        $quizPerformance = Quiz::withCount('attempts')
            ->withAvg('attempts as avg_score', 'score')
            ->get()
            ->map(function ($quiz) {
                return [
                    'title' => $quiz->title,
                    'attempts' => $quiz->attempts_count,
                    'avg_score' => round($quiz->avg_score, 1),
                ];
            });
        
        $studentPerformance = User::where('user_role', 'student')
            ->withCount('quizAttempts')
            ->withAvg('quizAttempts as avg_score', 'score')
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'attempts' => $user->quiz_attempts_count,
                    'avg_score' => round($user->avg_score, 1),
                ];
            });
        
        return view('instructor.analytics.index', compact(
            'totalQuizzes', 
            'totalStudents', 
            'totalAttempts',
            'quizPerformance',
            'studentPerformance'
        ));
    }
}