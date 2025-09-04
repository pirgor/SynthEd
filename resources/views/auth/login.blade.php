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
                                class="form-control border-start-0 @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
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
                                required autocomplete="current-password" placeholder="Password">
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

                        <!-- Forgot Password -->
                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a class="text-white text-decoration-underline" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
