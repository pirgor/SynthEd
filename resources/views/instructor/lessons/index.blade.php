@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Course Content</h4>
    </div>
    <div class="container py-4">
        <div class="accordion" id="lessonsAccordion">
            @foreach ($lessons as $index => $lesson)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} lesson-accordion-btn"
                            type="button" data-bs-toggle="collapse" data-bs-target="#lesson{{ $index }}"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="lesson{{ $index }}">
                            Lesson {{ $loop->iteration }}: {{ $lesson->title }}
                        </button>
                    </h2>

                    <div id="lesson{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
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
                                        <a href="{{ route('uploads.view', $upload->id) }}" target="_blank"
                                            class="reading-link">
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
                                    <li class="mb-2 box-item">
                                        <a href="#" class="reading-link">
                                            Assessment: {{ $quiz->title }}
                                        </a>
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
@endsection
