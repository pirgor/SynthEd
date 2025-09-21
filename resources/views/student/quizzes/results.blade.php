@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $quiz->title }} - Results</h2>

    <p><strong>Score:</strong> {{ $attempt->score }} / {{ $attempt->total_questions }}</p>
    <hr>

    <h3>AI Feedback</h3>

    @if($attempt->feedback)
        <ul>
            @foreach(explode("\n", $attempt->feedback) as $line)
                @if(trim($line))
                    <li>{{ $line }}</li>
                @endif
            @endforeach
        </ul>
    @else
        <p>No feedback available.</p>
    @endif

    <div class="mt-3">
        <a href="{{ route('student.quizzes.attempts', $quiz) }}" class="btn btn-primary">View My Attempts</a>
        <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary">Back to Quizzes</a>
    </div>
</div>
@endsection
