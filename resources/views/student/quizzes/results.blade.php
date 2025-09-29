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
                $studentAnswerId = $attempt->answers
                    ->where('question_id', $question->id)
                    ->pluck('option_id')
                    ->first();
                $correctOptionId = $question->options->where('is_correct', true)->pluck('id')->first();
            @endphp

            @foreach($question->options as $option)
                @php
                    $isStudentAnswer = $studentAnswerId === $option->id;
                    $isCorrectAnswer = $correctOptionId === $option->id;

                    if ($isCorrectAnswer) {
                        $bgColor = '#d4edda'; // green for correct
                    } elseif ($isStudentAnswer && !$isCorrectAnswer) {
                        $bgColor = '#f8d7da'; // red for wrong
                    } else {
                        $bgColor = '#f1f1f1'; // neutral
                    }
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
        </div>
    @endforeach
</div>
@endsection
