@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Lesson</h1>
        <a href="{{ route('instructor.lessons.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Lessons
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Lesson</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.lessons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Lesson Title --}}
                <div class="form-group">
                    <label for="title">Lesson Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Lesson Description --}}
                <div class="form-group">
                    <label for="description">Lesson Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                {{-- Lesson Upload --}}
                <div class="form-group">
                    <label for="file">Upload Lesson Material</label>
                    <input type="file" class="form-control-file @error('file') is-invalid @enderror" 
                           id="file" name="file">
                    <small class="form-text text-muted">
                        Supported formats: PDF, DOCX, PPTX, MP4, etc. (Max: 20MB)
                    </small>
                    @error('file')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Lesson</button>
                <a href="#" class="btn btn-success ml-2 disabled" id="addQuizBtn">
                    <i class="fas fa-plus"></i> Add Quiz
                </a>
            </form>
        </div>
    </div>
</div>

{{-- Script: Enable "Add Quiz" button after Lesson is saved --}}
@if(session('new_lesson_id'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let addQuizBtn = document.getElementById("addQuizBtn");
            addQuizBtn.classList.remove("disabled");
            addQuizBtn.href = "{{ url('instructor/quizzes/create?lesson_id=' . session('new_lesson_id')) }}";
        });
    </script>
@endif
@endsection
