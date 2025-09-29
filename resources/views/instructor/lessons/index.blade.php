@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Course Content</h4>
    </div>
    <div class="container py-4">
        <div class="accordion" id="lessonsAccordion">
            {{-- Only show to instructors --}}
            @if (Auth::user()->user_role === 'instructor')
                <div class="mb-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('instructor.lessons.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create Lesson
                    </a>

                    <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-info">
                        <i class="bi bi-plus-circle"></i> Create Quiz
                    </a>
                </div>
            @endif

            @foreach ($lessons as $index => $lesson)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} lesson-accordion-btn"
                            type="button" data-bs-toggle="collapse" data-bs-target="#lesson{{ $index }}"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="lesson{{ $index }}">
                            Lesson {{ $loop->iteration }}: {{ $lesson->title }}
                        </button>
                    </h2>

                    <div id="lesson{{ $index }}"
                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $index }}" data-bs-parent="#lessonsAccordion">

                        <div class="accordion-body">

                            {{-- Lesson Description --}}
                            @if ($lesson->description)
                                <p class="text-muted">{{ $lesson->description }}</p>
                            @endif

                            {{-- Lesson Files --}}
                            <ul class="list-unstyled">
                                @forelse($lesson->uploads as $upload)
                                    <li class="mb-2 box-item">
                                        <a href="{{ route('uploads.view', $upload->id) }}" class="reading-link">
                                            READING: {{ pathinfo($upload->file_name, PATHINFO_FILENAME) }}
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-muted">No files uploaded</li>
                                @endforelse
                            </ul>

                            {{-- Lesson Quizzes --}}
                            <ul class="list-unstyled">
                                @forelse($lesson->quizzes as $quiz)
                                    <li class="mb-2 box-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span>Assessment: {{ $quiz->title }}</span><br>
                                            @if ($quiz->deadline)
                                                <small class="text-muted">
                                                    Deadline:
                                                    {{ \Carbon\Carbon::parse($quiz->deadline)->format('F d, Y h:i A') }}
                                                </small>
                                            @else
                                                <small class="text-muted">No deadline</small>
                                            @endif
                                        </div>

                                        @php
                                            $hasAttempt = $quiz->attempts->isNotEmpty();
                                            $deadlinePassed = $quiz->deadline && now()->greaterThan($quiz->deadline);
                                        @endphp

                                        <div class="d-flex gap-2">
                                            @if (!$hasAttempt && !$deadlinePassed)
                                                <a href="{{ route('student.quizzes.take', $quiz) }}"
                                                    class="btn btn-primary btn-sm take-quiz-btn"
                                                    data-title="{{ $quiz->title }}">
                                                    Take Quiz
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    Not Available
                                                </button>
                                            @endif

                                            @php
                                                $latestAttempt = $quiz
                                                    ->attempts()
                                                    ->where('user_id', auth()->id())
                                                    ->latest()
                                                    ->first();
                                            @endphp

                                            @if ($latestAttempt)
                                                <a href="{{ route('student.quizzes.results', [$quiz->id, $latestAttempt->id]) }}"
                                                    class="btn btn-info btn-sm">
                                                    Results
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-muted">No quizzes available</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.querySelectorAll('.take-quiz-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    const quizTitle = this.dataset.title;

                    Swal.fire({
                        title: 'Start Quiz?',
                        html: `<p>You are about to start <strong>${quizTitle}</strong>.</p>
                           <p><b>Note:</b> You only have <span style="color:red;">one attempt</span> for this quiz.</p>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, start now',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        </script>
    @endsection

@endsection
