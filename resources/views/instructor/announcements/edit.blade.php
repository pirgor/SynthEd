@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Announcement</h1>
        <a href="{{ route('instructor.announcements.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Announcement</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.announcements.update', $announcement->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message', $announcement->message) }}</textarea>
                    @error('message')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Recipients</label>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="recipients_all" name="recipients" value="all" class="custom-control-input" {{ isset($announcement->data['recipients']) && $announcement->data['recipients'] === 'all' ? 'checked' : '' }} disabled>
                        <label class="custom-control-label" for="recipients_all">All Students</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="recipients_specific" name="recipients" value="specific" class="custom-control-input" {{ isset($announcement->data['recipients']) && $announcement->data['recipients'] === 'specific' ? 'checked' : '' }} disabled>
                        <label class="custom-control-label" for="recipients_specific">Specific Students</label>
                    </div>
                    <small class="form-text text-muted">Note: Recipients cannot be changed after creation.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Announcement</button>
            </form>
        </div>
    </div>
</div>
@endsection