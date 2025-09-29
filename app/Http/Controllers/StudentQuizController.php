<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use Illuminate\Support\Facades\Http;
use App\Models\ProgressTracking;
class StudentQuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::all(); // or filter for active quizzes if needed
        return view('student.quizzes.index', compact('quizzes'));
    }
    // Show quiz to student
    public function take(Quiz $quiz)
    {
        // ⏳ Check if deadline has passed
        if ($quiz->deadline && now()->greaterThan($quiz->deadline)) {
            return redirect()->route('student.quizzes.index')
                ->with('swal_error', 'This quiz is no longer available. The deadline has passed.');
        }

        // 🔒 Check if student already attempted
        $hasAttempted = $quiz->attempts()->where('user_id', auth()->id())->exists();
        if ($hasAttempted) {
            return redirect()->route('student.quizzes.index')
                ->with('swal_error', 'You have already attempted this quiz.');
        }

        $quiz->load('questions.options');
        return view('student.quizzes.take', compact('quiz'));
    }


    public function grades()
    {
        // Fetch all attempts for the logged-in student
        $attempts = \App\Models\QuizAttempt::with('quiz')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.quizzes.grades', compact('attempts'));
    }

    // Submit answers
    public function submit(Request $request, Quiz $quiz)
    {
        $answers = $request->input('answers', []);
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        $incorrectQuestions = [];

        foreach ($quiz->questions as $question) {
            $correctOption = $question->options()->where('is_correct', 1)->first();
            if (isset($answers[$question->id]) && $answers[$question->id] == $correctOption->id) {
                $score++;
            } else {
                // Save incorrect question + correct answer
                $incorrectQuestions[] = [
                    'question_text' => $question->question_text,
                    'correct_answer' => $correctOption->option_text
                ];
            }
        }

        // Build AI feedback prompt based on incorrect questions
        if ($score === $totalQuestions) {
            $feedbackPrompt = "The student answered all questions correctly. Provide 1-2 sentences of brief, encouraging feedback.";
        } else {
            $feedbackPrompt = "The student answered the following questions incorrectly:\n";

            foreach ($incorrectQuestions as $iq) {
                $feedbackPrompt .= "- Question: {$iq['question_text']}\n";
                $feedbackPrompt .= "  Correct answer: {$iq['correct_answer']}\n";
            }

            $feedbackPrompt .= "\nProvide concise, constructive feedback in 2-3 sentences, focusing strictly on these mistakes and how to improve. Do not include any introductory phrases.";
        }

        // Format for Gemini
        $contents = [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $feedbackPrompt]
                ]
            ]
        ];


        // Call Gemini for feedback
        $feedback = $this->getGeminiReply($contents);

        // Save attempt
        $attempt = \App\Models\QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => $score,
            'total_questions' => $totalQuestions,
            'feedback' => $feedback,
        ]);

        ProgressTracking::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
                'type' => 'quiz',
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );
        foreach ($answers as $questionId => $optionId) {
            \App\Models\QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'option_id' => $optionId,
            ]);
        }

        return redirect()->route('student.quizzes.results', [$quiz, $attempt])
            ->with('success', 'Quiz submitted successfully!');
    }


    /**
     * Fixed Gemini API call
     */
    private function getGeminiReply(array $contents)
    {
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
            'contents' => $contents
        ]);

        if ($response->successful()) {
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no reply.';
        }

        return 'Error reaching Gemini.';
    }


    // Show results
    public function results(Quiz $quiz, \App\Models\QuizAttempt $attempt)
    {
        $attempt->load('answers');
        $quiz->load('questions.options');
        return view('student.quizzes.results', compact('quiz', 'attempt'));
    }

    // Show history of attempts
    public function attempts(Quiz $quiz)
    {
        $attempts = $quiz->attempts()->where('user_id', auth()->id())->latest()->get();
        return view('student.quizzes.attempts', compact('quiz', 'attempts'));
    }
}
