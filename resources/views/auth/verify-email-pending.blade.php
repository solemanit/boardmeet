@extends('auth.layouts.app')

@section('title', 'Verify Your Email')

@section('content')
    <div class="auth-card py-6">
        <div class="card shadow-xl">
            <div class="card-body py-9 px-6 px-sm-12">
                <div class="mb-7">
                    <div class="text-center">
                        @php
                            $userEmail = $email ?? auth()->user()->email ?? null;
                            $user = $userEmail ? \App\Models\User::where('email', $userEmail)->first() : null;
                            $isVerified = $user && $user->email_verified_at;
                        @endphp

                        <div class="mb-4">
                            @if($isVerified)
                                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto; color: #27ae60;">
                                    <circle cx="40" cy="40" r="38" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M25 40L35 50L55 30" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @else
                                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin: 0 auto; color: #3498db;">
                                    <rect x="10" y="25" width="60" height="40" rx="4" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <path d="M10 30L40 50L70 30" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @endif
                        </div>

                        @if($isVerified)
                            <h4 class="mb-2 fw-semibold">Email Verified! ✓</h4>
                            <p class="text-muted mb-1">Your email has been successfully verified:</p>
                            <p class="fw-semibold badge bg-success text-white">{{ $userEmail }}</p>
                        @else
                            <h4 class="mb-2 fw-semibold">Verify Your Email Address</h4>
                            <p class="text-muted mb-1">We've sent a verification link to:</p>
                            <p class="fw-semibold badge bg-light text-dark">{{ $userEmail }}</p>
                        @endif
                    </div>
                </div>

                @if($isVerified)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Great news!</strong> Your email address has been verified and your account is now active.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @else
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <svg class="bi bi-info-circle flex-shrink-0 me-2" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="m8.93.456-.934-.694-.934.694-.6-.8.934-.694-.934-.694.6-.8.934.694.934-.694.6.8-.934.694.934.694-.6.8zm0 1.561-.934-.694-.934.694-.6-.8.934-.694-.934-.694.6-.8.934.694.934-.694.6.8-.934.694.934.694-.6.8z"/>
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="m8.93-2.456-.934-.694-.934.694-.6-.8.934-.694-.934-.694.6-.8.934.694.934-.694.6.8-.934.694.934.694-.6.8zm0 1.561-.934-.694-.934.694-.6-.8.934-.694-.934-.694.6-.8.934.694.934-.694.6.8-.934.694.934.694-.6.8z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 4zm0 6a.5.5 0 1 1 0 1 .5.5 0 0 1 0-1z"/>
                        </svg>
                        <strong>Check your spam folder</strong> if you don't see the email within a few minutes.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($isVerified)
                    <div class="mb-6">
                        <p class="text-muted mb-4">
                            <strong>What's next?</strong>
                        </p>
                        <ol class="text-muted small">
                            <li>Go to the login page</li>
                            <li>Sign in with your email and password</li>
                            <li>Access your dashboard</li>
                        </ol>
                    </div>
                @else
                    <div class="mb-6">
                        <p class="text-muted mb-4">
                            <strong>What's next?</strong>
                        </p>
                        <ol class="text-muted small">
                            <li>Click the verification link in the email</li>
                            <li>Your account will be activated immediately</li>
                            <li>You'll be redirected to the login page</li>
                            <li>Sign in with your email and password</li>
                        </ol>
                    </div>
                @endif

                @if(!$isVerified)
                    <div class="mb-4">
                        <p class="text-muted text-center mb-3">
                            <strong>Didn't receive the email?</strong>
                        </p>
                        <form action="{{ route('resend-verification-email') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ $userEmail }}">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-arrow-clockwise"></i> Resend Verification Email
                            </button>
                        </form>
                    </div>
                @endif

                <hr class="my-4">

                <p class="text-center text-muted small mb-0">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        @if($isVerified)
                            Go to Login →
                        @else
                            Back to Login
                        @endif
                    </a>
                </p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
@endsection
