@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $quiz->title }}</h2>
    <hr>

    <form method="POST" action="{{ route('student.quizzes.submit', $quiz) }}">
        @csrf
        @foreach($quiz->questions as $question)
            <div class="mb-4">
                <p><strong>{{ $question->question_text }}</strong></p>

                @foreach($question->options as $option)
                    <div style="background-color: #f1f1f1; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                        <label style="margin: 0; cursor: pointer; width: 100%;">
                            <input type="radio" 
                                   name="answers[{{ $question->id }}]" 
                                   value="{{ $option->id }}" 
                                   style="margin-right: 8px;">
                            {{ $option->option_text }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Submit Quiz</button>
    </form>
</div>
@endsection
