@extends('layouts.app')

@section('content')
    <!-- Full-width Top Bar -->
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Greetings, {{ Auth::user()->name }}!</h4>
    </div>

    <!-- Page Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Notifications -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Notifications</div>
                    <div class="card-body">
                        <p class="text-muted">No new notifications.</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Upcoming Deadlines</div>
                    <div class="card-body">
                        @if ($upcomingDeadlines->isEmpty())
                            <p class="text-muted">No deadlines yet.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach ($upcomingDeadlines as $quiz)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold">{{ $quiz->title }}</span><br>
                                            <small class="text-muted">
                                                Due: {{ \Carbon\Carbon::parse($quiz->deadline)->format('M d, Y h:i A') }}
                                            </small>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-danger text-white me-2">
                                                {{ \Carbon\Carbon::parse($quiz->deadline)->diffForHumans() }}
                                            </span>

                                            @php
                                                $hasAttempted = \App\Models\QuizAttempt::where('user_id', auth()->id())
                                                    ->where('quiz_id', $quiz->id)
                                                    ->exists();
                                            @endphp

                                            @if (!$hasAttempted)
                                                <a href="{{ route('student.quizzes.take', $quiz) }}"
                                                    class="btn btn-primary btn-sm take-quiz-btn"
                                                    data-title="{{ $quiz->title }}">
                                                    Take Quiz
                                                </a>
                                            @else
                                                <span class="badge bg-success">Completed</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Progress -->
        <div class="card shadow-sm p-3 mb-4">
            <h6 class="font-weight-bold text-dark mb-2">Overall Progress</h6>

            <div class="progress" style="height: 24px; border-radius: 50px; overflow: hidden;">
                <div class="progress-bar bg-gradient-success progress-bar-striped progress-bar-animated" role="progressbar"
                    style="width: {{ $progressPercent }}%; transition: width 0.6s ease;"
                    aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                    {{ $progressPercent }}%
                </div>
            </div>

            <div class="text-right mt-2 small text-muted">
                {{ $progressPercent < 100 ? 'Keep going, you’re almost there!' : 'Well done! 🎉' }}
            </div>
        </div>
    </div>
@endsection
