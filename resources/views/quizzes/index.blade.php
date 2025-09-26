@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Assessment Management</h4>
    </div>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quizzes as $quiz)
                <tr>
                    <td>{{ $quiz->title }}</td>
                    <td>{{ $quiz->description }}</td>
                    <td>
                        <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('instructor.quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this quiz?')">Delete</button>
                        </form>
                        <a href="{{ route('instructor.quizzes.questions.index', $quiz) }}"
                            class="btn btn-sm btn-info">Questions</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('instructor.quizzes.create') }}" class="btn btn-primary">Create New Quiz</a>
@endsection
