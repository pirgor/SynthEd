@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Assessment Management</h4>
    </div>
    <form method="POST" action="{{ route('instructor.quizzes.store') }}" class="card shadow p-4">
        @csrf

        {{-- Quiz Title --}}
        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Quiz Title</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter quiz title"
                value="{{ old('title') }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter quiz description">{{ old('description') }}</textarea>
        </div>

        {{-- Deadline --}}
        <div class="mb-3">
            <label for="deadline" class="form-label fw-bold">Deadline</label>
            <input type="datetime-local" name="deadline" id="deadline" class="form-control">
            <div class="form-text">Students will not be able to take the quiz after the deadline.</div>
        </div>

        {{-- Lesson Select --}}
        <div class="mb-3">
            <label for="lesson_id" class="form-label fw-bold">Select Lesson</label>
            <select name="lesson_id" id="lesson_id" class="form-control" required>
                <option value="">-- Choose a Lesson --</option>
                @foreach ($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                        {{ $lesson->title }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-plus-circle"></i> Create Quiz
            </button>
        </div>
    </form>
@endsection
