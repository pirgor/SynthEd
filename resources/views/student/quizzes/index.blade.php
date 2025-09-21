@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Available Quizzes</h1>

        @if ($quizzes->isEmpty())
            <p>No quizzes available at the moment.</p>
        @else
            <div class="row">
                @foreach ($quizzes as $quiz)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $quiz->title }}</h5>
                                <p class="card-text">{{ $quiz->description ?? '' }}</p>
                                <a href="{{ route('student.quizzes.take', $quiz) }}" class="btn btn-primary">Take Quiz</a>
                                <a href="{{ route('student.quizzes.attempts', $quiz) }}" class="btn btn-secondary">View My
                                    Attempts</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
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
