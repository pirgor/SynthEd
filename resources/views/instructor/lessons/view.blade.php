@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
            <h4 class="mb-0">{{ $upload->file_name }}</h4>
        </div>
        <br/>
        <div class="row">
            <!-- PDF Viewer -->
            <div class="col-md-9">
                <iframe src="{{ asset('storage/' . $upload->file_path) }}" width="100%" height="800px" style="border: none;">
                </iframe>
            </div>

            <!-- Controls + Audio -->
            <div class="col-md-3 d-flex flex-column gap-3">
                <button id="generate" class="btn btn-success w-100">Generate Audio</button>
                <button id="clear" class="btn btn-secondary w-100">Clear Audio Player</button>
                <a href="{{ route('student.lessons.practice', $lesson->id) }}" class="btn btn-primary">
                    <i class="fas fa-flask"></i> Generate Practice Quiz
                </a>
                <a href="{{ asset('storage/' . $upload->file_path) }}" class="btn btn-primary w-100" download>
                    Download File
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
                    body: JSON.stringify({
                        text
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
