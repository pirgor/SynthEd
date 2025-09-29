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
        $totalQuestions = $quiz->questions->count();
        $score = 0;
        $incorrectQuestions = [];

        // 1️⃣ Create attempt first
        $attempt = \App\Models\QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'score' => 0,
            'total_questions' => $totalQuestions,
            'feedback' => '',
        ]);

        // 2️⃣ Process each question
        foreach ($quiz->questions as $question) {
            $answer = $answers[$question->id] ?? null;

            switch ($question->type) {

                case 'multiple_choice':
                case 'true_false':
                    $correctOption = $question->options()->where('is_correct', 1)->first();
                    if ($answer && $answer == $correctOption->id) {
                        $score++;
                    } else {
                        $incorrectQuestions[] = [
                            'question_text' => $question->question_text,
                            'correct_answer' => $correctOption->option_text,
                            'type' => $question->type,
                        ];
                    }

                    \App\Models\QuizAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => $answer ?? null,
                        'answer_text' => null,
                    ]);
                    break;

                case 'multiple_answers':
                    $correctOptionIds = $question->options()->where('is_correct', 1)->pluck('id')->toArray();
                    $submittedIds = is_array($answer) ? $answer : [];

                    if (!array_diff($correctOptionIds, $submittedIds) && !array_diff($submittedIds, $correctOptionIds)) {
                        $score++;
                    } else {
                        $incorrectQuestions[] = [
                            'question_text' => $question->question_text,
                            'correct_answer' => implode(', ', $question->options()->where('is_correct', 1)->pluck('option_text')->toArray()),
                            'type' => $question->type,
                        ];
                    }

                    foreach ($submittedIds as $optionId) {
                        \App\Models\QuizAnswer::create([
                            'quiz_attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                            'option_id' => $optionId,
                            'answer_text' => null,
                        ]);
                    }
                    break;

                case 'identification':
                    $correctAnswer = $question->options()->where('is_correct', 1)->first()->option_text ?? '';
                    if ($answer && strcasecmp(trim($answer), trim($correctAnswer)) === 0) {
                        $score++;
                    } else {
                        $incorrectQuestions[] = [
                            'question_text' => $question->question_text,
                            'correct_answer' => $correctAnswer,
                            'type' => $question->type,
                        ];
                    }

                    \App\Models\QuizAnswer::create([
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => null,
                        'answer_text' => trim((string) $answer),
                    ]);
                    break;

                case 'matching':
                    $correctMatches = $question->options()->pluck('match_key', 'id')->toArray();
                    $submittedMatches = is_array($answer) ? $answer : [];
                    $isCorrect = true;

                    foreach ($submittedMatches as $index => $value) {
                        $optionId = $question->options[$index]->id ?? null;

                        if (!$optionId || strtolower(trim((string)$value)) !== strtolower($correctMatches[$optionId])) {
                            $isCorrect = false;
                        }

                        \App\Models\QuizAnswer::create([
                            'quiz_attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                            'option_id' => $optionId,
                            'answer_text' => trim((string) $value),
                        ]);
                    }

                    if ($isCorrect) {
                        $score++;
                    } else {
                        $incorrectQuestions[] = [
                            'question_text' => $question->question_text,
                            'correct_answer' => implode(', ', $correctMatches),
                            'type' => $question->type,
                        ];
                    }
                    break;
            }
        }

        // 3️⃣ Build AI feedback prompt
        if ($score === $totalQuestions) {
            $feedbackPrompt = "The student answered all questions correctly. Provide 1-2 sentences of brief, encouraging feedback.";
        } else {
            $feedbackPrompt = "The student answered the following questions incorrectly:\n";

            foreach ($incorrectQuestions as $iq) {
                $feedbackPrompt .= "- Question: {$iq['question_text']}\n";
                $feedbackPrompt .= "  Correct answer(s): {$iq['correct_answer']}\n";

                if ($iq['type'] === 'identification') {
                    $feedbackPrompt .= "  Note: Answers are case-insensitive and may have multiple acceptable forms.\n";
                }
                if ($iq['type'] === 'multiple_answers') {
                    $feedbackPrompt .= "  Note: Multiple selections may be correct; all correct options must be chosen.\n";
                }
                if ($iq['type'] === 'matching') {
                    $feedbackPrompt .= "  Note: Match pairs should be evaluated based on all correct key-value pairs.\n";
                }
            }

            $feedbackPrompt .= "\nProvide concise, constructive feedback in 2-3 sentences, focusing strictly on these mistakes and how to improve. Do not include introductory phrases.";
        }

        // 4️⃣ Call Gemini API
        $contents = [['role' => 'user', 'parts' => [['text' => $feedbackPrompt]]]];
        $feedback = $this->getGeminiReply($contents);

        // 5️⃣ Update attempt with score & feedback
        $attempt->update([
            'score' => $score,
            'feedback' => $feedback,
        ]);

        // 6️⃣ Track progress
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
