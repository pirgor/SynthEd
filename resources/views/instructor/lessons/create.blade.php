@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="w-100 p-3 mb-4" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Create Lesson</h4>
    </div>
    
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">New Lesson Form</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.lessons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Lesson Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lesson Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Lesson Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lesson Upload --}}
                <div class="mb-4">
                    <label for="file" class="form-label">Upload Lesson Material</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                           id="file" name="file">
                    <small class="form-text text-muted">
                        Supported formats: PDF, DOCX, PPTX, MP4, etc. (Max: 20MB)
                    </small>
                    @error('file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Lesson</button>
                    <a href="#" class="btn btn-success disabled" id="addQuizBtn">
                        <i class="fas fa-plus"></i> Add Quiz
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script: Enable "Add Quiz" button after Lesson is saved --}}
@if(session('new_lesson_id'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const addQuizBtn = document.getElementById("addQuizBtn");
            addQuizBtn.classList.remove("disabled");
            addQuizBtn.href = "{{ route('instructor.quizzes.create', ['lesson_id' => session('new_lesson_id')]) }}";
        });
    </script>
@endif
@endsection
