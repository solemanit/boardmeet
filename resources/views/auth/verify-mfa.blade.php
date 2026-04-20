@extends('auth.layouts.app')

@section('title', 'Verify MFA')

@section('content')
    <div class="auth-card py-6">
        <div class="card shadow-xl">
            <div class="card-body p-5 text-center">

                <i class="bi bi-shield-check" style="font-size: 3rem; color: #3498db;"></i>

                <h5 class="mt-3 fw-semibold">Verify Your Identity</h5>
                <p class="text-muted">
                    We've sent a verification code to your <strong>{{ session('mfa_method', 'email') }}</strong>.
                </p>

                <form action="{{ route('auth.verify-mfa') }}" method="POST">
                    @csrf

                    {{-- FIX: Removed hidden user_id and temp_token inputs.
                         Both are now read from the session in AuthWebController::verifyMfa().
                         Keeping them in the form allowed a malicious user to tamper with
                         user_id and bypass account ownership. --}}

                    @if ($errors->any())
                        <div class="alert alert-danger text-start">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="otp" class="form-label">Verification Code</label>
                        <input type="text" name="otp" id="otp"
                            class="form-control @error('otp') is-invalid @enderror"
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            value="{{ old('otp') }}"
                            required autofocus
                            style="font-size: 2rem; text-align: center; letter-spacing: 8px;">
                        @error('otp')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Verify
                        </button>
                    </div>
                </form>

                <p class="text-muted mt-4 mb-0 small">
                    <a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a>
                </p>

            </div>
        </div>
    </div>
@endsection
