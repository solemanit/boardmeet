@extends('auth.layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-card py-6">
        <div class="card shadow-xl">
            <div class="card-body py-9 px-6 px-sm-12">
                <div class="mb-7">
                    <div class="d-flex align-items-center justify-content-center mb-1">
                        <img class="app-main-logo logo-black" width="200" src="{{ asset('assets/img/logo/logo.png') }}" alt="BoardMeet">
                        <img class="app-main-logo logo-white d-none" width="200" src="{{ asset('assets/img/logo/logo-white.png') }}" alt="BoardMeet">
                    </div>
                    <div class="text-center">
                        <h4 class="mb-1 fw-semibold">Reset Your Password</h4>
                        <p>Enter your email address and we'll send you a password reset link.</p>
                    </div>
                </div>

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Success message (email sent) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- FIX: Error message (email not found / send failure) — was completely missing --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="you@example.com"
                            value="{{ old('email') }}"
                            required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <p class="text-muted small">
                            We'll send a password reset link to your email address. The link will expire in 60 minutes.
                        </p>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Send Password Reset Link
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <p class="text-muted small mb-0">
                        Remember your password?
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none text-hover-underline">Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
