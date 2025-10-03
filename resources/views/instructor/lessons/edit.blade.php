@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="w-100 p-3 mb-4" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Edit Lesson</h4>
    </div>
    
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Update Lesson</h6>
        </div>
        <div class="card-body">
            <form id="editLessonForm" action="{{ route('instructor.lessons.update', $lesson->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Lesson Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" 
                           value="{{ old('title', $lesson->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lesson Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Lesson Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $lesson->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Current Upload --}}
                @if($lesson->uploads->first())
                    <div class="mb-3">
                        <p><strong>Current File:</strong> {{ $lesson->uploads->first()->file_name }}</p>
                    </div>
                @endif

                {{-- Replace Upload --}}
                <div class="mb-4">
                    <label for="file" class="form-label">Replace Lesson Material (optional)</label>
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
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                    <a href="{{ route('instructor.lessons.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('editLessonForm').addEventListener('submit', function(e) {
    e.preventDefault(); // stop form from submitting immediately

    Swal.fire({
        title: 'Update Lesson?',
        text: "Are you sure you want to save these changes?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form via fetch
            let form = e.target;
            let formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => {
                if (res.ok) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Lesson has been updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('instructor.lessons.index') }}";
                    });
                } else {
                    Swal.fire('Error', 'Something went wrong while updating.', 'error');
                }
            }).catch(() => {
                Swal.fire('Error', 'Could not connect to server.', 'error');
            });
        }
    });
});
</script>
@endsection
