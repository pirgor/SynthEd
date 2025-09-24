<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('questions', 'attempts')->get();
        return view('instructor.assessments.index', compact('quizzes'));
    }
    
    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options', 'attempts.user');
        $attempts = $quiz->attempts()->with('user')->latest()->get();
        
        return view('instructor.assessments.show', compact('quiz', 'attempts'));
    }
    
    public function results(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->with('user')->get();
        $scores = $attempts->pluck('score');
        
        $stats = [
            'average' => $scores->avg(),
            'highest' => $scores->max(),
            'lowest' => $scores->min(),
            'attempts' => $attempts->count(),
        ];
        
        return view('instructor.assessments.results', compact('quiz', 'attempts', 'stats'));
    }
}