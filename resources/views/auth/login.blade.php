@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100" style="background-color: #ffffff;">
        <div class="col-md-4 text-center">

            <!-- Branding row (icon + text side by side) -->
            <div class="d-flex align-items-center justify-content-center mb-4">
                <i class="bi bi-book" style="font-size: 5rem; color: #000;"></i>
                <h1 class="ms-3 fw-bold" style="color: #000; font-size: 3.5rem;">SynthEd</h1>
            </div>

            <!-- Login Box -->
            <div class="card shadow-sm p-4" style="background-color: #224D3D; border-radius: 10px;">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input id="email" type="email"
                                class="form-control border-start-0 @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" type="password"
                                class="form-control border-start-0 @error('password') is-invalid @enderror"
                                name="password" required autocomplete="current-password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-light fw-bold">
                                LOGIN
                            </button>
                        </div>

                        <!-- Forgot Password / Register -->
                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <p class="mb-1">
                                    <a class="text-white text-decoration-underline" href="{{ route('password.request') }}">
                                        Forgot your password?
                                    </a>
                                </p>
                                @if (Route::has('register'))
                                    <p class="mb-0 text-white">
                                        Don’t have an account?
                                        <a class="text-white fw-bold text-decoration-underline"
                                            href="{{ route('register') }}">
                                            Register here
                                        </a>
                                    </p>
                                @endif
                            </div>
                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="termsModalLabel">🧾 SynthEd Terms and Conditions</h5>
                </div>
                <div class="modal-body text-start">

                    <h6>1. Introduction</h6>
                    <p>Welcome to SynthEd, an AI-assisted e-learning platform designed to enhance IT education through
                        interactive content, AI-generated quizzes, and automated feedback. By creating an account or
                        accessing SynthEd, you agree to comply with these Terms and Conditions. Please read them carefully.
                    </p>

                    <h6>2. General Terms (Applies to All Users)</h6>
                    <p><strong>Acceptance of Terms:</strong> By accessing or using SynthEd, users agree to these Terms, the
                        Privacy Policy, and all applicable laws and regulations.</p>
                    <p><strong>Use of Platform:</strong> Users must use SynthEd only for educational and lawful purposes.
                        Misuse of the system, including attempts to disrupt functionality or misuse AI features, is strictly
                        prohibited.</p>
                    <p><strong>Intellectual Property:</strong> All learning materials, AI-generated content, and system
                        designs are the property of SynthEd or its licensors. Users may not copy, distribute, or modify
                        materials without permission.</p>
                    <p><strong>AI-Generated Content Disclaimer:</strong> SynthEd uses artificial intelligence (Google
                        Gemini) to generate learning materials, practice quizzes, and explanations. While the system strives
                        for accuracy, users are advised that AI outputs may contain minor inaccuracies and should be used as
                        supplementary learning support.</p>
                    <p><strong>Data Privacy:</strong> Personal information and learning analytics are collected solely for
                        academic improvement and system evaluation, in accordance with data protection standards (e.g., RA
                        10173 – Data Privacy Act of 2012).</p>
                    <p><strong>Limitation of Liability:</strong> SynthEd and its developers shall not be held responsible for
                        any data loss, content inaccuracies, or system errors arising from the use of AI features.</p>

                    <h6>3. Student User Terms</h6>
                    <p><strong>Account Responsibility:</strong> Students are responsible for maintaining the confidentiality
                        of their login credentials and must not share accounts with others.</p>
                    <p><strong>AI-Generated Quizzes:</strong> Practice quizzes generated by AI are for self-assessment
                        purposes only. Scores obtained are not official grades and should not be considered academic
                        evaluations.</p>
                    <p><strong>Feedback and AI Interaction:</strong> Students acknowledge that AI chatbot responses are for
                        educational assistance only and do not replace formal instruction.</p>
                    <p><strong>Content Usage:</strong> Students may download or view materials for personal study but may not
                        redistribute them publicly or commercially.</p>

                    <h6>4. Instructor User Terms</h6>
                    <p><strong>Assessment Creation:</strong> Instructors may use the AI-assisted assessment generator but are
                        responsible for reviewing and approving all AI-generated items before publishing them.</p>
                    <p><strong>Analytics Access:</strong> Instructors have access to quiz performance summaries and
                        engagement data to improve course delivery.</p>
                    <p><strong>Material Ownership:</strong> Uploaded teaching materials remain the intellectual property of
                        the instructor, but SynthEd reserves the right to index and use them for AI content generation within
                        the platform.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" id="agreeBtn" class="btn btn-success">I Agree</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script: Show Modal Only Once -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const hasAgreed = localStorage.getItem("synthEdAgreed");

            if (!hasAgreed) {
                const termsModal = new bootstrap.Modal(document.getElementById("termsModal"));
                termsModal.show();

                document.getElementById("agreeBtn").addEventListener("click", function () {
                    localStorage.setItem("synthEdAgreed", "true");
                    termsModal.hide();
                });
            }
        });
    </script>
@endsection
