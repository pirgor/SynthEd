@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100" style="background-color: #ffffff;">
        <div class="col-md-6 text-center">

            <!-- Branding row (icon + text side by side) -->
            <div class="d-flex align-items-center justify-content-center mb-4">
                <i class="bi bi-book" style="font-size: 5rem; color: #000;"></i>
                <h1 class="ms-3 fw-bold" style="color: #000; font-size: 3.5rem;">SynthEd</h1>
            </div>

            <!-- Register Box -->
            <div class="card shadow-sm p-4 mx-auto" style="background-color: #224D3D; border-radius: 10px; max-width: 600px;">

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input id="name" type="text"
                                class="form-control border-start-0 @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Full Name">
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Student ID Number -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <input id="student_id" type="text"
                                class="form-control border-start-0 @error('student_id') is-invalid @enderror"
                                name="student_id" value="{{ old('student_id') }}" required placeholder="Student ID Number">
                            @error('student_id')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" type="email"
                                class="form-control border-start-0 @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" placeholder="Email Address">
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
                                class="form-control border-start-0 @error('password') is-invalid @enderror" name="password"
                                required autocomplete="new-password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input id="password-confirm" type="password" class="form-control border-start-0"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm Password">
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-2">
                            <button type="submit" class="btn btn-light fw-bold">
                                REGISTER
                            </button>
                        </div>

                        <!-- Already have account -->
                        <div class="text-center">
                            <a class="text-white text-decoration-underline" href="{{ route('login') }}">
                                Already registered? Login here
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
