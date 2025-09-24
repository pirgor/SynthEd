@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Content</h1>
        <a href="{{ route('instructor.content.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload New Content</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('instructor.content.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">Content Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="file">Select File</label>
                    <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" name="file" required>
                    <small class="form-text text-muted">Supported formats: PDF, DOCX, TXT (Max: 10MB)</small>
                    @error('file')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Content</button>
            </form>
        </div>
    </div>
</div>
@endsection