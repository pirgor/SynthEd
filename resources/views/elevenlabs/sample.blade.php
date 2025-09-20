@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Sample Text to Speech</h2>

        <div class="mb-3">
            <textarea id="sample-text" class="form-control" rows="5">
This is a sample text that will be converted to speech using ElevenLabs.
        </textarea>
        </div>

        <div class="mb-3">
            <button id="generate" class="btn btn-primary">Generate Speech</button>
            <button id="clear" class="btn btn-secondary">Clear All Players</button>
        </div>

        <div id="audio-container" class="mt-4"></div>
    </div>

    <script>
        document.getElementById('generate').addEventListener('click', () => {
            const text = document.getElementById('sample-text').value.trim();

            if (!text) {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Please enter some text first.',
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

            fetch('/speech-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        text
                        // no more voice_id or speed — backend pulls from user settings
                    })
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
                        text: 'Your text has been converted to speech.',
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
