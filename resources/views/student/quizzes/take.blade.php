@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $quiz->title }}</h2>
        <hr>

        <form method="POST" action="{{ route('student.quizzes.submit', $quiz) }}">
            @csrf
            @foreach ($quiz->questions as $question)
                <div class="mb-4">
                    <p><strong>{{ $question->question_text }}</strong></p>

                    @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                        @foreach ($question->options as $option)
                            <div
                                style="background-color: #f1f1f1; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                                <label style="margin: 0; cursor: pointer; width: 100%;">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                        style="margin-right: 8px;">
                                    {{ $option->option_text }}
                                </label>
                            </div>
                        @endforeach
                    @elseif($question->type === 'multiple_answers')
                        @foreach ($question->options as $option)
                            <div
                                style="background-color: #f1f1f1; padding: 5px 10px; border-radius: 5px; margin-bottom: 5px;">
                                <label style="margin: 0; cursor: pointer; width: 100%;">
                                    <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}"
                                        style="margin-right: 8px;">
                                    {{ $option->option_text }}
                                </label>
                            </div>
                        @endforeach
                    @elseif($question->type === 'identification')
                        <input type="text" name="answers[{{ $question->id }}]" class="form-control"
                            placeholder="Type your answer here" required>
                    @elseif($question->type === 'matching')
                        @php
                            // Collect all possible match values
                            $matchOptions = $question->options()->pluck('match_key')->shuffle()->toArray();
                        @endphp

                        <div class="matching-grid mb-3"
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; align-items: center;">
                            @foreach ($question->options as $option)
                                <!-- Left side -->
                                <div>
                                    <strong>{{ $option->option_text }}</strong>
                                </div>

                                <!-- Right side dropdown -->
                                <div>
                                    <select name="answers[{{ $question->id }}][{{ $loop->index }}]"
                                        class="form-select w-100" required>
                                        <option value="">Select match...</option>
                                        @foreach ($matchOptions as $match)
                                            <option value="{{ $match }}">{{ $match }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Submit Quiz</button>
        </form>
    </div>
@endsection
