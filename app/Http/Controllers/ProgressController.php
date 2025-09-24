<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index()
    {
        $students = User::where('user_role', 'student')
            ->withCount('quizAttempts')
            ->withAvg('quizAttempts as avg_score', 'score')
            ->get();
            
        return view('instructor.progress.index', compact('students'));
    }
    
    public function show(User $user)
    {
        $attempts = $user->quizAttempts()
            ->with('quiz')
            ->latest()
            ->get();
            
        $quizzes = Quiz::withCount('questions')->get();
        
        return view('instructor.progress.show', compact('user', 'attempts', 'quizzes'));
    }
    
    public function quizReport(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with('user')
            ->latest()
            ->get();
            
        $questions = $quiz->questions()->with('options')->get();
        
        return view('instructor.progress.quiz-report', compact('quiz', 'attempts', 'questions'));
    }
}