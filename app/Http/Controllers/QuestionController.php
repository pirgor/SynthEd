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
        //dd($request->all());
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required|boolean'
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $request->question_text
        ]);

        foreach ($request->options as $option) {
            $question->options()->create([
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct']
            ]);
        }

        return redirect()->route('quizzes.questions.index', $quiz)->with('success', 'Question added!');
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

        return redirect()->route('quizzes.questions.index', $quiz)->with('success', 'Question updated!');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();
        return redirect()->route('quizzes.questions.index', $quiz)->with('success', 'Question deleted!');
    }
}
