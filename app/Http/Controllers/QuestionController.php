<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;

class QuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->with('options')->get();
        return view('quizzes.questions.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz)
    {
        return view('quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        // Validate input
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string|in:multiple_choice,multiple_answers,true_false,identification,matching',
            'options' => 'sometimes|array',
            'options.*.option_text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.match_key' => 'nullable|string',
            'answer_text' => 'required_if:type,identification|string',
            'correct_options' => 'nullable|array', // for multiple_answers
            'correct_options.*' => 'integer'
        ]);

        // Create question
        $question = $quiz->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
        ]);

        // Handle question types
        if (in_array($request->type, ['multiple_choice', 'true_false', 'matching'])) {
            foreach ($request->options as $option) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? 0,
                    'match_key' => $option['match_key'] ?? null,
                ]);
            }
        } elseif ($request->type === 'multiple_answers') {
            foreach ($request->options as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => in_array($index, $request->correct_options ?? []) ? 1 : 0,
                    'match_key' => null,
                ]);
            }
        } elseif ($request->type === 'identification') {
            $question->options()->create([
                'option_text' => $request->answer_text,
                'is_correct' => 1
            ]);
        }

        return redirect()->route('instructor.quizzes.questions.index', $quiz)
            ->with('success', 'Question added!');
    }


    public function edit(Quiz $quiz, Question $question)
    {
        return view('quizzes.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ]);

        $question->update(['question_text' => $request->question_text]);
        $question->options()->delete();
        foreach ($request->options as $option) {
            $question->options()->create($option);
        }

        return redirect()->route('instructor.quizzes.questions.index', $quiz)->with('success', 'Question updated!');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('instructor.quizzes.questions.index', $quiz)->with('success', 'Question deleted!');
    }
}
