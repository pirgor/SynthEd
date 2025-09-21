@extends('layouts.app')

@section('content')
<h2>{{ $quiz->title }}</h2>

<form method="POST" action="{{ route('student.quizzes.submit', $quiz) }}">
    @csrf
    @foreach($quiz->questions as $question)
        <div class="mb-4">
            <p><strong>{{ $question->question_text }}</strong></p>
            @foreach($question->options as $option)
                <div>
                    <label>
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}">
                        {{ $option->option_text }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach

    <button type="submit" class="btn btn-success">Submit Quiz</button>
</form>
@endsection
