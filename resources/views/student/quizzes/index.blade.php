@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">📘 Available Quizzes</h1>

    @if ($quizzes->isEmpty())
        <div class="alert alert-info text-center">
            No quizzes available at the moment.
        </div>
    @else
        <div class="row">
            @foreach ($quizzes as $quiz)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $quiz->title }}</h5>
                            <p class="text-muted mb-2">{{ $quiz->description ?? 'No description provided.' }}</p>

                            {{-- Deadline --}}
                            @if ($quiz->deadline)
                                <span class="badge 
                                    {{ now()->greaterThan($quiz->deadline) ? 'bg-danger' : 'bg-success' }} 
                                    align-self-start mb-3">
                                    {{ now()->greaterThan($quiz->deadline) ? 'Closed' : 'Deadline: ' . $quiz->deadline->format('M d, Y h:i A') }}
                                </span>
                            @endif

                            <div class="mt-auto d-flex gap-2">
                                @if (!$quiz->attempts()->where('user_id', auth()->id())->exists() && (!$quiz->deadline || now()->lessThanOrEqualTo($quiz->deadline)))
                                    <a href="{{ route('student.quizzes.take', $quiz) }}" class="btn btn-primary btn-sm flex-fill">
                                        Take Quiz
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                        Not Available
                                    </button>
                                @endif
                                <a href="{{ route('student.quizzes.attempts', $quiz) }}" class="btn btn-outline-dark btn-sm flex-fill">
                                    My Attempts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- SweetAlert Error --}}
@if (session('swal_error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: "{{ session('swal_error') }}",
            confirmButtonText: 'OK'
        });
    </script>
@endif
@endsection
