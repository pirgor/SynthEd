@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Header --}}
        <div class="w-100 p-3 mb-4" style="background-color: #224D3D; color: white;">
            <h4 class="mb-0">{{ $upload->file_name }}</h4>
        </div>

        <div class="row">
            <!-- PDF Viewer -->
            <div class="col-md-9">
                <iframe src="{{ asset('storage/' . $upload->file_path) }}" width="100%" height="800px" style="border: none;">
                </iframe>

                {{-- Summary Container (moved here) --}}
                <div id="summary-container" class="mt-3"></div>
            </div>

            <!-- Controls -->
            <div class="col-md-3 d-flex flex-column gap-3">
                {{-- Generate Audio --}}
                <button id="generate" class="btn btn-success w-100">
                    <i class="bi bi-volume-up"></i> Generate Audio
                </button>

                {{-- Generate Summary --}}
                <button id="summary" class="btn btn-outline-success w-100">
                    <i class="bi bi-card-text"></i> Generate Summary
                </button>

                {{-- Clear Audio --}}
                <button id="clear" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Clear Audio Player
                </button>

                {{-- Practice Quiz --}}
                <a href="{{ route('student.lessons.practice', $lesson->id) }}" class="btn btn-primary w-100"
                    style="background-color:#224D3D; border:none;">
                    <i class="fas fa-flask"></i> Generate Practice Quiz
                </a>

                {{-- Download File --}}
                <a href="{{ asset('storage/' . $upload->file_path) }}" class="btn btn-outline-dark w-100" download>
                    <i class="bi bi-download"></i> Download File
                </a>

                {{-- Audio Player --}}
                <div id="audio-container" class="mt-3"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Generate Audio ---
        document.getElementById('generate').addEventListener('click', () => {
            const text = @json($upload->extracted_text);

            if (!text || text.trim().length === 0) {
                Swal.fire('No text found', 'This PDF has no extracted text available.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Generating speech...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("/speech-generate", {
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

                    document.getElementById('audio-container').innerHTML = '';
                    document.getElementById('audio-container').appendChild(player);

                    Swal.fire('Done!', 'Your PDF text has been converted to speech.', 'success');
                })
                .catch(err => {
                    Swal.fire('Error', err.message, 'error');
                });
        });
        // --- Clear Audio ---
        document.getElementById('clear').addEventListener('click', () => {
            document.getElementById('audio-container').innerHTML = '';
        });
    </script>
    <script>
        // --- Generate Summary ---
        document.getElementById('summary').addEventListener('click', () => {
            const text = @json($upload->extracted_text);

            if (!text || text.trim().length === 0) {
                Swal.fire('No text found', 'This PDF has no extracted text available.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Generating summary...',
                text: 'Please wait while we analyze the content.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch("{{ route('student.summary.generate') }}", {
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
                    if (!res.ok) throw new Error("Failed to fetch summary");
                    return res.json();
                })
                .then(data => {
                    Swal.close();

                    let summaryData = data.summary;

                    // 🔧 Fix: If summary is a stringified JSON, parse it
                    if (typeof summaryData === "string") {
                        try {
                            summaryData = JSON.parse(summaryData);
                        } catch (e) {
                            // If it's just plain text, keep it as string
                        }
                    }

                    const container = document.getElementById('summary-container');
                    container.innerHTML = `
                <div class="card border-success mt-3">
                    <div class="card-header bg-success text-white">
                        <strong>Generated Summary</strong>
                    </div>
                    <div class="card-body">
                        ${renderSummary(summaryData)}
                    </div>
                </div>
            `;
                })
                .catch(err => {
                    Swal.fire('Error', err.message, 'error');
                });
        });

        // --- Recursive formatter for study guide JSON or plain text ---
        function renderSummary(data) {
            // Case: plain string
            if (typeof data === "string") {
                return `<p style="white-space: pre-line">${data}</p>`;
            }

            // Case: nested object (study guide style)
            if (typeof data === "object" && data !== null) {
                let html = "<ul class='list-group list-group-flush'>";
                for (const key in data) {
                    html += `
                    <li class="list-group-item">
                        <strong>${key}</strong><br>
                        ${renderSummary(data[key])}
                    </li>
                `;
                }
                html += "</ul>";
                return html;
            }

            return `<p>${String(data)}</p>`;
        }
    </script>
@endsection
