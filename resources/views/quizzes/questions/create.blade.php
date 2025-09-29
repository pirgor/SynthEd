@extends('layouts.app')

@section('content')
<div class="w-100 p-3" style="background-color: #224D3D; color: white;">
    <h4 class="mb-0">Create Question for: {{ $quiz->title }}</h4>
</div>

<form method="POST" action="{{ route('instructor.quizzes.questions.store', $quiz) }}" id="question-form">
    @csrf
    <div class="container">
        <!-- Question Text -->
        <div class="mb-3">
            <label>Question:</label>
            <textarea name="question_text" class="form-control" required>{{ old('question_text') }}</textarea>
        </div>

        <!-- Question Type Selector -->
        <div class="mb-3">
            <label>Question Type:</label>
            <select name="type" id="question-type" class="form-control" required>
                <option value="multiple_choice" {{ old('type')=='multiple_choice'?'selected':'' }}>Multiple Choice (Single Answer)</option>
                <option value="multiple_answers" {{ old('type')=='multiple_answers'?'selected':'' }}>Multiple Answers (Checkboxes)</option>
                <option value="true_false" {{ old('type')=='true_false'?'selected':'' }}>True / False</option>
                <option value="identification" {{ old('type')=='identification'?'selected':'' }}>Identification / Fill in the Blank</option>
                <option value="matching" {{ old('type')=='matching'?'selected':'' }}>Matching</option>
            </select>
        </div>

        <!-- Options Container (dynamic) -->
        <div id="options-container" class="mb-3"></div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="{{ route('instructor.quizzes.questions.index', $quiz) }}" class="btn btn-secondary">Back to Questions</a>
    </div>
</form>

<script>
const typeSelect = document.getElementById('question-type');
const container = document.getElementById('options-container');

// Render inputs dynamically based on selected question type
function renderOptions(type) {
    container.innerHTML = '';

    if (type === 'multiple_choice') {
        for (let i = 0; i < 4; i++) {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `
                <input type="text" name="options[${i}][option_text]" placeholder="Option ${i+1}" class="form-control d-inline-block w-75" required>
                <input type="radio" name="correct_option" value="${i}" class="ms-2" required> Correct
            `;
            container.appendChild(div);
        }
    } else if (type === 'multiple_answers') {
        for (let i = 0; i < 4; i++) {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `
                <input type="text" name="options[${i}][option_text]" placeholder="Option ${i+1}" class="form-control d-inline-block w-75" required>
                <input type="checkbox" name="correct_options[]" value="${i}" class="ms-2"> Correct
            `;
            container.appendChild(div);
        }
    } else if (type === 'true_false') {
        const options = ['True', 'False'];
        options.forEach((opt, i) => {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `
                <input type="text" name="options[${i}][option_text]" value="${opt}" class="form-control d-inline-block w-75" readonly>
                <input type="radio" name="correct_option" value="${i}" class="ms-2" required> Correct
            `;
            container.appendChild(div);
        });
    } else if (type === 'identification') {
        container.innerHTML = `
            <label>Answer:</label>
            <input type="text" name="answer_text" class="form-control" required>
        `;
    } else if (type === 'matching') {
        for (let i = 0; i < 4; i++) {
            const div = document.createElement('div');
            div.classList.add('mb-2');
            div.innerHTML = `
                <input type="text" name="options[${i}][option_text]" placeholder="Left item" class="form-control d-inline-block w-50" required>
                <input type="text" name="options[${i}][match_key]" placeholder="Right item" class="form-control d-inline-block w-50" required>
            `;
            container.appendChild(div);
        }
    }
}

// Initial render
renderOptions(typeSelect.value);
typeSelect.addEventListener('change', e => renderOptions(e.target.value));

// Handle correct options on submit
document.getElementById('question-form').addEventListener('submit', function(e) {
    const form = this;
    const type = typeSelect.value;

    if (type === 'multiple_choice' || type === 'true_false') {
        const selected = document.querySelector('input[name="correct_option"]:checked');
        if (!selected) { alert('Please select the correct option'); e.preventDefault(); return; }

        const correctIndex = parseInt(selected.value);
        form.querySelectorAll('input[name^="options"][name$="[is_correct]"]').forEach(el => el.remove());

        const count = type === 'multiple_choice' ? 4 : 2;
        for (let i = 0; i < count; i++) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `options[${i}][is_correct]`;
            hidden.value = i === correctIndex ? 1 : 0;
            form.appendChild(hidden);
        }
    } else if (type === 'multiple_answers') {
        const checked = Array.from(document.querySelectorAll('input[name="correct_options[]"]:checked'))
                             .map(c => parseInt(c.value));
        if (checked.length === 0) { alert('Select at least one correct option'); e.preventDefault(); return; }

        form.querySelectorAll('input[name^="options"][name$="[is_correct]"]').forEach(el => el.remove());
        for (let i = 0; i < 4; i++) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `options[${i}][is_correct]`;
            hidden.value = checked.includes(i) ? 1 : 0;
            form.appendChild(hidden);
        }
    }
});
</script>
@endsection
