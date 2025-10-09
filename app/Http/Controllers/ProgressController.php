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
        // Get the latest attempt for this quiz (for example)
        $latestAttempt = $quiz->attempts()
            ->with('user')
            ->latest()
            ->first();

        if (!$latestAttempt) {
            return redirect()->back()->with('error', 'No attempts found for this quiz.');
        }

        // Redirect to the student-style results page
        return redirect()->route('quizzes.results', [
            'quiz' => $quiz->id,
            'attempt' => $latestAttempt->id,
        ]);
    }
}
