@extends('layouts.app')

@section('content')
<div class="container">
    <h3>{{ $lesson->title }} - Practice Quiz</h3>

    {{-- Form to generate --}}
    <form id="generateQuizForm" action="{{ route('student.lessons.practice.generate', $lesson) }}" method="POST" class="mb-4">
        @csrf
        <label>Number of Questions:</label>
        <input type="number" name="question_count" min="1" max="10" value="5" class="form-control w-25 mb-2">
        <button type="submit" class="btn btn-primary">Generate Practice Quiz</button>
    </form>

    @if(session('practice_quiz'))
        <form action="#" method="POST" id="practiceQuizForm">
            @csrf
            @foreach(session('practice_quiz') as $index => $q)
                <div class="card mb-3 p-3">
                    <strong>Q{{ $index + 1 }}: {{ $q['question_text'] }}</strong>
                    <ul class="list-unstyled mt-2">
                        @foreach($q['options'] as $i => $opt)
                            <li>
                                <label class="option-label" data-correct="{{ $opt['is_correct'] }}">
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $i }}">
                                    {{ $opt['option_text'] }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    {{-- Hidden explanation / read more --}}
                    <div class="mt-2 text-muted explanation" style="display:none;">
                        <small>{{ $q['explanation'] ?? '' }}</small>
                        @if(isset($q['read_more']))
                            <div class="mt-1"><a href="{{ $q['read_more'] }}" target="_blank">Read more</a></div>
                        @endif
                    </div>
                </div>
            @endforeach

            <button type="button" class="btn btn-success" id="checkAnswers">Check Answers</button>
        </form>
    @endif
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('generateQuizForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;

    Swal.fire({
        title: 'Generate Practice Quiz?',
        text: 'This will generate new questions from the lesson material.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading alert
            Swal.fire({
                title: 'Generating Quiz...',
                text: 'Please wait while we fetch questions.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    form.submit(); // Submit the form
                }
            });
        }
    });
});

document.getElementById('checkAnswers')?.addEventListener('click', function() {
    const questions = @json(session('practice_quiz'));
    const form = document.getElementById('practiceQuizForm');
    let score = 0;
    let feedback = '';

    questions.forEach((q, index) => {
        const selected = form.querySelector(`input[name="answers[${index}]"]:checked`);
        const explanationDiv = form.querySelectorAll('.explanation')[index];

        // Show explanation / read more
        explanationDiv.style.display = 'block';

        form.querySelectorAll('.option-label')[index * 4].parentElement.parentElement.querySelectorAll('.option-label').forEach(label => {
            label.classList.remove('bg-success', 'bg-danger', 'text-white');
        });

        q.options.forEach((opt, optIndex) => {
            const label = form.querySelectorAll('.option-label')[index * 4 + optIndex];
            const isSelected = selected && selected.value == optIndex;
            if (opt.is_correct) {
                label.classList.add('bg-success', 'text-white');
            } else if (isSelected && !opt.is_correct) {
                label.classList.add('bg-danger', 'text-white');
            }
        });

        if (selected && q.options[selected.value].is_correct) score++;
    });

    Swal.fire({
        title: `Your score: ${score} / ${questions.length}`,
        icon: 'info'
    });
});
</script>

{{-- Styles --}}
<style>
.option-label {
    display: block;
    padding: 5px 10px;
    border-radius: 5px;
    margin-bottom: 5px;
}
</style>
@endsection
