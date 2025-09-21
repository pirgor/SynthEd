@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('instructor.quizzes.store') }}">
    @csrf
    <input type="text" name="title" placeholder="Quiz title" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button type="submit">Create Quiz</button>
</form>
@endsection