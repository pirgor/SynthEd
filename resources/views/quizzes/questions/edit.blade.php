@extends('layouts.app')

@section('content')
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Edit Question for: {{ $quiz->title }}</h4>
    </div>
    <form method="POST" action="{{ route('instructor.quizzes.questions.update', [$quiz, $question]) }}" id="question-form">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="mb-3">
                <label>Question:</label>
                <textarea name="question_text" class="form-control" required>{{ old('question_text', $question->question_text) }}</textarea>
            </div>

            <div class="mb-3">
                <label>Options (select the correct one):</label>
                @foreach ($question->options as $i => $option)
                    <div class="mb-2">
                        <input type="text" name="options[{{ $i }}][option_text]"
                            class="form-control d-inline-block w-75" placeholder="Option {{ $i + 1 }}"
                            value="{{ old('options.' . $i . '.option_text', $option->option_text) }}" required>
                        <input type="radio" name="correct_option" value="{{ $i }}" class="ms-2"
                            {{ old('correct_option', $option->is_correct) ? 'checked' : '' }} required> Correct
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Update Question</button>
            <a href="{{ route('instructor.quizzes.questions.index', $quiz) }}" class="btn btn-secondary">Back to
                Questions</a>
        </div>
    </form>

    <script>
        document.getElementById('question-form').addEventListener('submit', function(e) {
            const form = this;
            const selected = document.querySelector('input[name="correct_option"]:checked');
            if (!selected) {
                alert('Please select the correct option');
                e.preventDefault();
                return;
            }

            const correctIndex = parseInt(selected.value);

            // Remove existing hidden is_correct inputs
            form.querySelectorAll('input[name^="options"][name$="[is_correct]"]').forEach(el => el.remove());

            // Append hidden is_correct inputs directly to the form
            for (let i = 0; i < 4; i++) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `options[${i}][is_correct]`;
                hidden.value = i === correctIndex ? 1 : 0;
                form.appendChild(hidden);
            }
        });
    </script>
@endsection
