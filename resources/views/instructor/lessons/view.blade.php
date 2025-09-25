@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3>{{ $upload->file_name }}</h3>

    <div class="row">
        <!-- PDF Viewer -->
        <div class="col-md-9">
            <iframe src="{{ asset('storage/'.$upload->file_path) }}" 
                    width="100%" height="800px" 
                    style="border: none;">
            </iframe>
        </div>

        <!-- Controls + Audio -->
        <div class="col-md-3 d-flex flex-column gap-3">
            <button id="generate" class="btn btn-success w-100">🎙 Generate Speech</button>
            <button id="clear" class="btn btn-secondary w-100">🗑 Clear All Players</button>

            <a href="{{ asset('storage/'.$upload->file_path) }}" 
               class="btn btn-primary w-100" 
               download>
                ⬇️ Download File
            </a>

            <div id="audio-container" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('generate').addEventListener('click', () => {
    const text = @json($upload->extracted_text);

    if (!text || text.trim().length === 0) {
        Swal.fire({
            title: 'No text found',
            text: 'This PDF has no extracted text available.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Generating speech...',
        text: 'Please wait while we process your request.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("{{ route('speech.generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ text })
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed to fetch audio");
            return res.blob();
        })
        .then(blob => {
            const audioUrl = URL.createObjectURL(blob);
            const player = document.createElement('audio');
            player.controls = true;
            player.src = audioUrl;
            player.autoplay = true;

            document.getElementById('audio-container').appendChild(player);

            Swal.fire({
                title: 'Done!',
                text: 'Your PDF text has been converted to speech.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        })
        .catch(err => {
            Swal.fire({
                title: 'Error',
                text: err.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
});

document.getElementById('clear').addEventListener('click', () => {
    document.getElementById('audio-container').innerHTML = '';
});
</script>
@endsection
