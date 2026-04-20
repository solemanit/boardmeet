@extends('auth.layouts.app')

@section('title', 'Login')

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
                        <h4 class="mb-1 fw-semibold">Welcome</h4>
                        <p>Login now to manage your meetings and board activities.</p>
                    </div>
                </div>

                <form action="{{ route('auth.login.submit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="**********" required>
                            <span class="input-group-text password-toggle">
                                <span class="close-eye password-eye">
                                    <svg width="22" height="10" viewBox="0 0 22 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 1C21 1 17 7 11 7C5 7 1 1 1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M14 6.5L15.5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M19 4L21 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M1 6L3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M8 6.5L6.5 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="open-eye password-eye d-none">
                                    <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.544 7.04498C20.848 7.4713 21 7.68447 21 8C21 8.31553 20.848 8.52869 20.544 8.95501C19.1779 10.8706 15.6892 15 11 15C6.31078 15 2.8221 10.8706 1.45604 8.95502C1.15201 8.5287 1 8.31553 1 8C1 7.68447 1.15201 7.47131 1.45604 7.04499C2.8221 5.12944 6.31078 1 11 1C15.6892 1 19.1779 5.12944 20.544 7.04498Z" stroke="currentColor" stroke-width="1.5" />
                                        <path d="M14 8C14 6.34315 12.6569 5 11 5C9.34315 5 8 6.34315 8 8C8 9.65685 9.34315 11 11 11C12.6569 11 14 9.65685 14 8Z" stroke="currentColor" stroke-width="1.5" />
                                    </svg>
                                </span>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <p class="m-0">
                                <a href="{{ route('password.request') }}" class="text-hover-underline">Lost Password?</a>
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </div>
                    </div>
                </form>

                <p class="text-center">Don't have an account? <a href="{{ route('auth.register') }}"
                        class="text-primary text-decoration-none text-hover-underline">Create a free account</a></p>

            </div>
        </div>
    </div>
@endsection
