@extends('layouts.app')

@section('content')
<h2>{{ $quiz->title }} - My Attempts</h2>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Score</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attempts as $attempt)
        <tr>
            <td>{{ $attempt->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $attempt->score }} / {{ $attempt->total_questions }}</td>
            <td>{{ $attempt->feedback }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
