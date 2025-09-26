@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">My Grades</h4>
    </div>

    @if ($attempts->isEmpty())
        <p>You haven't attempted any quizzes yet.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Quiz</th>
                    <th>Score</th>
                    <th>Date Attempted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attempts as $attempt)
                    @php
                        $percentage =
                            $attempt->total_questions > 0 ? ($attempt->score / $attempt->total_questions) * 100 : 0;

                        if ($percentage >= 80) {
                            $bgColor = '#d4edda'; // light green
                        } elseif ($percentage >= 50) {
                            $bgColor = '#fff3cd'; // light orange
                        } else {
                            $bgColor = '#f8d7da'; // light red
                        }
                    @endphp
                    <tr>
                        <td>{{ $attempt->quiz->title }}</td>
                        <td>
                            <div
                                style="background-color: {{ $bgColor }}; padding: 5px 10px; border-radius: 5px; text-align: center; font-weight: bold;">
                                {{ $attempt->score }} / {{ $attempt->total_questions }}
                            </div>
                        </td>
                        <td>{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('student.quizzes.results', [$attempt->quiz, $attempt]) }}"
                                class="btn btn-sm btn-info">
                                View Feedback
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection
