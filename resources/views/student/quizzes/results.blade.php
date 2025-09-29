@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $quiz->title }} - Results</h2>

    <p><strong>Score:</strong> {{ $attempt->score }} / {{ $attempt->total_questions }}</p>
    <hr>

    <h3>AI Feedback</h3>
    @if($attempt->feedback)
        <div class="alert alert-info">
            @foreach(explode("\n", $attempt->feedback) as $line)
                @if(trim($line))
                    <p>{{ $line }}</p>
                @endif
            @endforeach
        </div>
    @else
        <p>No feedback available.</p>
    @endif

    <hr>

    <h3>Question Breakdown</h3>
    @foreach($quiz->questions as $question)
        <div class="mb-4">
            <p><strong>{{ $question->question_text }}</strong></p>

            @php
                $studentAnswers = $attempt->answers->where('question_id', $question->id);
            @endphp

            @if(in_array($question->type, ['multiple_choice', 'true_false']))
                @php
                    $studentAnswerId = $studentAnswers->pluck('option_id')->first();
                    $correctOptionId = $question->options->where('is_correct', true)->pluck('id')->first();
                @endphp

                @foreach($question->options as $option)
                    @php
                        $isStudentAnswer = $studentAnswerId === $option->id;
                        $isCorrectAnswer = $correctOptionId === $option->id;
                        $bgColor = $isCorrectAnswer ? '#d4edda' : ($isStudentAnswer ? '#f8d7da' : '#f1f1f1');
                    @endphp

                    <div style="background-color: {{ $bgColor }}; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                        <input type="radio" disabled {{ $isStudentAnswer ? 'checked' : '' }}>
                        {{ $option->option_text }}
                        @if($isCorrectAnswer)
                            <span class="badge bg-success">Correct</span>
                        @elseif($isStudentAnswer && !$isCorrectAnswer)
                            <span class="badge bg-danger">Your Answer</span>
                        @endif
                    </div>
                @endforeach

            @elseif($question->type === 'multiple_answers')
                @php
                    $studentOptionIds = $studentAnswers->pluck('option_id')->toArray();
                    $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                @endphp

                @foreach($question->options as $option)
                    @php
                        $isStudentAnswer = in_array($option->id, $studentOptionIds);
                        $isCorrectAnswer = in_array($option->id, $correctOptionIds);
                        $bgColor = $isCorrectAnswer ? '#d4edda' : ($isStudentAnswer ? '#f8d7da' : '#f1f1f1');
                    @endphp

                    <div style="background-color: {{ $bgColor }}; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                        <input type="checkbox" disabled {{ $isStudentAnswer ? 'checked' : '' }}>
                        {{ $option->option_text }}
                        @if($isCorrectAnswer)
                            <span class="badge bg-success">Correct</span>
                        @elseif($isStudentAnswer && !$isCorrectAnswer)
                            <span class="badge bg-danger">Your Answer</span>
                        @endif
                    </div>
                @endforeach

            @elseif($question->type === 'identification')
                @php
                    $studentAnswerText = $studentAnswers->pluck('answer_text')->first();
                    $correctAnswerText = $question->options->where('is_correct', true)->pluck('option_text')->first();
                    $isCorrect = strcasecmp(trim($studentAnswerText), trim($correctAnswerText)) === 0;
                    $bgColor = $isCorrect ? '#d4edda' : '#f8d7da';
                @endphp
                <div style="background-color: {{ $bgColor }}; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                    <strong>Your Answer:</strong> {{ $studentAnswerText ?? 'No answer' }}<br>
                    <strong>Correct Answer:</strong> {{ $correctAnswerText }}
                </div>

            @elseif($question->type === 'matching')
                @php
                    $correctMatches = $question->options->pluck('match_key', 'id')->toArray();
                @endphp
                <ul>
                    @foreach($studentAnswers as $ans)
                        @php
                            $optionText = $question->options->where('id', $ans->option_id)->pluck('option_text')->first() ?? 'Unknown';
                            $correctValue = $correctMatches[$ans->option_id] ?? 'Unknown';
                            $isCorrect = strtolower($ans->answer_text) === strtolower($correctValue);
                            $bgColor = $isCorrect ? '#d4edda' : '#f8d7da';
                        @endphp
                        <li style="background-color: {{ $bgColor }}; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                            <strong>{{ $optionText }}:</strong> Your Answer: {{ $ans->answer_text }} | Correct: {{ $correctValue }}
                        </li>
                    @endforeach
                </ul>

            @endif

        </div>
    @endforeach
</div>
@endsection
