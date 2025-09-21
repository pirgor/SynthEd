@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Generate Questions for: {{ $quiz->title }}</h2>

        <form method="POST" action="{{ route('quizzes.quizzes.generate.post', $quiz) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">Upload file (PDF, DOCX, TXT):</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="question_count" class="form-label">Number of questions:</label>
                <input type="number" name="question_count" id="question_count" class="form-control" value="5"
                    min="1" max="20" required>
            </div>

            <button type="submit" class="btn btn-primary">Generate Questions</button>
            <a href="{{ route('quizzes.questions.index', $quiz) }}" class="btn btn-secondary">Back to Questions</a>
        </form>
    </div>
@endsection
