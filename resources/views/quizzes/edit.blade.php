@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Quiz: {{ $quiz->title }}</h2>

    <form method="POST" action="{{ route('instructor.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Quiz Title:</label>
            <input type="text" name="title" id="title" class="form-control" 
                   value="{{ old('title', $quiz->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $quiz->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Quiz</button>
        <a href="{{ route('instructor.quizzes.index') }}" class="btn btn-secondary">Back to Quizzes</a>
    </form>
</div>
@endsection
