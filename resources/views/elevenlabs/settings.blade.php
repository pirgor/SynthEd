@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Text-to-Speech Settings</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('tts.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="voice_id" class="form-label">Voice</label>
                <select class="form-select" name="voice_id" id="voice_id">
                    <option value="onwK4e9ZLuTAKqWW03F9"
                        {{ $settings->voice_id == 'onwK4e9ZLuTAKqWW03F9' ? 'selected' : '' }}>Male</option>
                    <option value="XrExE9yKIg1WjnnlVkGX"
                        {{ $settings->voice_id == 'XrExE9yKIg1WjnnlVkGX' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="speed" class="form-label">Playback Speed (0.7 - 1.2)</label>
                <input type="range" class="form-range" id="speed" name="speed" min="0.7" max="1.2"
                    step="0.05" value="{{ $settings->speed }}">
                <div>Current: <span id="speed-display">{{ $settings->speed }}</span>x</div>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <script>
        document.getElementById('speed').addEventListener('input', e => {
            document.getElementById('speed-display').textContent = e.target.value;
        });
    </script>
@endsection
