@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('quizzes.questions.store', $quiz) }}" id="question-form">
    @csrf
    <div class="container">
        <h2>Create Question for: {{ $quiz->title }}</h2>

        <div class="mb-3">
            <label>Question:</label>
            <textarea name="question_text" class="form-control" required>{{ old('question_text') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Options (select the correct one):</label>
            @for ($i = 0; $i < 4; $i++)
                <div class="mb-2">
                    <input type="text" name="options[{{ $i }}][option_text]" class="form-control d-inline-block w-75" 
                        placeholder="Option {{ $i + 1 }}" value="{{ old('options.'.$i.'.option_text') }}" required>
                    <input type="radio" name="correct_option" value="{{ $i }}" class="ms-2" required 
                        {{ old('correct_option') == $i ? 'checked' : '' }}> Correct
                </div>
            @endfor
        </div>

        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="{{ route('quizzes.questions.index', $quiz) }}" class="btn btn-secondary">Back to Questions</a>
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

    // Remove any existing hidden is_correct inputs first
    form.querySelectorAll('input[name^="options"][name$="[is_correct]"]').forEach(el => el.remove());

    // Append hidden is_correct inputs **directly to the form**
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
