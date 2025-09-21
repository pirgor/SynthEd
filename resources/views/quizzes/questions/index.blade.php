@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Questions for: {{ $quiz->title }}</h1>

        <a href="{{ route('quizzes.questions.create', $quiz) }}" class="btn btn-primary mb-3">Add New Question</a>
        <a href="{{ route('quizzes.index') }}" class="btn btn-secondary mb-3">Back to Quizzes</a>
        <a href="{{ route('quizzes.quizzes.generate', $quiz) }}" class="btn btn-success mb-3">Generate Questions</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($questions->count() > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Options</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $index => $question)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $question->question_text }}</td>
                            <td>
                                <ul>
                                    @foreach ($question->options as $option)
                                        <li>
                                            {{ $option->option_text }}
                                            @if ($option->is_correct)
                                                <strong>(Correct)</strong>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <a href="{{ route('quizzes.questions.edit', [$quiz, $question]) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('quizzes.questions.destroy', [$quiz, $question]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this question?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No questions found for this quiz yet.</p>
        @endif
    </div>
@endsection
