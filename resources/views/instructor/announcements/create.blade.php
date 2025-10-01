@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Announcement</h1>
        <a href="{{ route('instructor.announcements.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">New Announcement</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.announcements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    @error('message')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Recipients</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="recipients_all" name="recipients" value="all" class="custom-control-input" checked>
                        <label class="custom-control-label" for="recipients_all">All Students</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="recipients_specific" name="recipients" value="specific" class="custom-control-input">
                        <label class="custom-control-label" for="recipients_specific">Specific Students</label>
                    </div>
                </div>
                
                <div class="form-group" id="student-selection" style="display: none;">
                    <label for="student_ids">Select Students</label>
                    <select class="form-control select2" id="student_ids" name="student_ids[]" multiple>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                    @error('student_ids')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Create Announcement</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientsAll = document.getElementById('recipients_all');
        const recipientsSpecific = document.getElementById('recipients_specific');
        const studentSelection = document.getElementById('student-selection');
        
        recipientsAll.addEventListener('change', function() {
            if (this.checked) {
                studentSelection.style.display = 'none';
            }
        });
        
        recipientsSpecific.addEventListener('change', function() {
            if (this.checked) {
                studentSelection.style.display = 'block';
            }
        });
        
        // Initialize Select2
        $('.select2').select2();
    });
</script>
@endpush
@endsection