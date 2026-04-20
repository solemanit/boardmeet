@extends('layouts.app')

@section('page-title', 'Profile')
@section('title', 'My Profile')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">My Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-6">

        {{-- ── Left: Avatar card ────────────────────────────────────────── --}}
        <div class="col-lg-4 col-xl-3">

            {{-- Avatar & meta --}}
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-body text-center py-8">
                    <div class="avatar rounded-pill d-flex align-items-center justify-content-center bg-primary text-white fw-bold mx-auto mb-4"
                        style="width:80px;height:80px;font-size:2rem;flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-semibold mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-custom-paragraph mb-3">{{ auth()->user()->email }}</p>
                    @if (auth()->user()->is_active)
                        <span class="badge badge-label-success">Active Account</span>
                    @else
                        <span class="badge badge-label-secondary">Inactive Account</span>
                    @endif
                </div>
                <div class="border-top px-6 py-5 d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Role</span>
                        <span class="badge badge-label-info">
                            {{ auth()->user()->role?->name ?? 'No Role' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Joined</span>
                        <span class="text-custom-body small fw-medium">
                            {{ auth()->user()->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Last Login</span>
                        <span class="text-custom-body small fw-medium">
                            {{ auth()->user()->last_login_at?->diffForHumans() ?? 'Never' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Password Changed</span>
                        <span class="text-custom-body small fw-medium">
                            {{ auth()->user()->last_password_changed_at?->format('M d, Y') ?? 'Never' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Security status --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mt-6">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C9.23858 2 7 4.23858 7 7V8H5C3.89543 8 3 8.89543 3 10V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V10C21 8.89543 20.1046 8 19 8H17V7C17 4.23858 14.7614 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9 8V7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="2" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </span>
                        Security Status
                    </h3>
                </div>
                <div class="pure-card-body d-flex flex-column gap-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Account Status</span>
                        @if (auth()->user()->is_active)
                            <span class="badge badge-label-success">Active</span>
                        @else
                            <span class="badge badge-label-danger">Locked</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Login Attempts</span>
                        <span class="text-custom-body small fw-semibold">
                            {{ auth()->user()->login_attempts ?? 0 }} / 5
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">MFA</span>
                        @if (auth()->user()->mfa_enabled)
                            <span class="badge badge-label-success">Enabled</span>
                        @else
                            <span class="badge badge-label-secondary">Disabled</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-custom-paragraph small">Permissions</span>
                        <span class="badge badge-label-primary">
                            {{ auth()->user()->getPermissions()->count() }}
                        </span>
                    </div>
                </div>
                <div class="pure-card-footer">
                    <a href="{{ route('activity.index') }}"
                        class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3M7 3C7 3 7 1 12 1C17 1 17 3 17 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        View Activity Log
                    </a>
                </div>
            </div>

        </div>

        {{-- ── Right: Forms ─────────────────────────────────────────────── --}}
        <div class="col-lg-8 col-xl-9">

            {{-- Basic Information --}}
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="pure-card-header">
                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                            <span class="text-primary d-flex align-items-center">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                    <path opacity="0.4" d="M12 14C7.02944 14 3 17.5817 3 22H21C21 17.5817 16.9706 14 12 14Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            Basic Information
                        </h3>
                    </div>

                    <div class="pure-card-body">
                        <div class="row row-cols-1 row-cols-md-2 gy-5">
                            <div class="col">
                                <label for="name" class="form-label fw-medium">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name"
                                    value="{{ old('name', auth()->user()->name) }}"
                                    placeholder="Enter your full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="email" class="form-label fw-medium">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    placeholder="Enter your email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="phone_number" class="form-label fw-medium">
                                    Phone Number
                                    <span class="text-custom-paragraph fw-normal ms-1">(Optional)</span>
                                </label>
                                <input type="tel"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', auth()->user()->phone_number) }}"
                                    placeholder="Enter your phone number">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label fw-medium">Role</label>
                                <div class="form-control d-flex align-items-center" style="cursor:default;background:var(--bs-secondary-bg,#f8f9fa)">
                                    <span class="badge badge-label-info">
                                        {{ auth()->user()->role?->name ?? 'No Role' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pure-card-footer d-flex align-items-center justify-content-end gap-3">
                        <button type="button" class="btn btn-outline-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mt-6">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C9.23858 2 7 4.23858 7 7V8H5C3.89543 8 3 8.89543 3 10V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V10C21 8.89543 20.1046 8 19 8H17V7C17 4.23858 14.7614 2 12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9 8V7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="2" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </span>
                        Change Password
                    </h3>
                </div>

                {{-- Toggle trigger --}}
                <div class="pure-card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <p class="text-custom-paragraph mb-1 small">Password</p>
                            <p class="mb-0 fw-medium">••••••••••••••••</p>
                        </div>
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-toggle="collapse" data-bs-target="#changePasswordForm"
                            aria-expanded="false" aria-controls="changePasswordForm">
                            Change Password
                        </button>
                    </div>
                </div>

                <div class="collapse" id="changePasswordForm">
                    <div class="border-top">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="pure-card-body border-bottom">
                                <div class="row row-cols-1 row-cols-md-2 gy-5">
                                    <div class="col col-md-12 col-lg-6">
                                        <label for="current_password" class="form-label fw-medium">Current Password</label>
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password"
                                            placeholder="Enter current password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <label for="new_password" class="form-label fw-medium">New Password</label>
                                        <input type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            id="new_password" name="new_password"
                                            placeholder="Enter new password">
                                        <small class="text-custom-paragraph mt-1 d-block">
                                            8+ chars, uppercase, lowercase, number, special character
                                        </small>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <label for="new_password_confirmation" class="form-label fw-medium">Confirm Password</label>
                                        <input type="password"
                                            class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                            id="new_password_confirmation" name="new_password_confirmation"
                                            placeholder="Confirm new password">
                                        @error('new_password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="pure-card-footer d-flex align-items-center justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-toggle="collapse" data-bs-target="#changePasswordForm">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Deactivate Account --}}
            <div class="pure-card rounded-custom card-bg shadow-custom mt-6">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-danger d-flex align-items-center">
                            <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M19.0693 8.32064C19.0693 8.3962 18.4774 15.8859 18.1394 19.038C17.9277 20.9724 16.6813 22.1457 14.8117 22.179C13.3753 22.2113 11.969 22.2224 10.5855 22.2224C9.11662 22.2224 7.68015 22.2113 6.2858 22.179C4.47888 22.1357 3.23142 20.9391 3.03053 19.038C2.68275 15.8748 2.10169 8.3962 2.09088 8.32064C2.08008 8.09287 2.15353 7.87621 2.30257 7.70067C2.44946 7.53845 2.66115 7.44067 2.88364 7.44067H18.2873C18.5087 7.44067 18.7096 7.53845 18.8684 7.70067C19.0164 7.87621 19.0909 8.09287 19.0693 8.32064Z" fill="currentColor"/>
                                <path d="M7.80859 17.9497L7.80859 11.3497" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M13.3594 17.9499L13.3594 11.3499" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M20.5755 4.41873C20.5755 3.96208 20.2159 3.60432 19.7838 3.60432H16.5459C15.887 3.60432 15.3146 3.13545 15.1677 2.47436L14.9863 1.66439C14.7324 0.685531 13.8565 0 12.8737 0H8.28886C7.29522 0 6.42794 0.685531 6.1644 1.71772L5.99483 2.47547C5.84687 3.13545 5.27444 3.60432 4.61669 3.60432H1.3787C0.945595 3.60432 0.585938 3.96208 0.585938 4.41873V4.84094C0.585938 5.28648 0.945595 5.65536 1.3787 5.65536H19.7838C20.2159 5.65536 20.5755 5.28648 20.5755 4.84094V4.41873Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Delete Account
                    </h3>
                </div>
                <div class="pure-card-body">
                    <form action="#" method="POST"
                        onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <div class="alert alert-danger d-flex align-items-start gap-3 mb-5">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <path d="M12 9V13M12 17H12.01M10.29 3.86L1.82 18C1.64 18.3 1.55 18.65 1.56 19C1.57 19.66 1.92 20.27 2.49 20.62C2.76 20.79 3.07 20.88 3.39 20.88H20.61C20.93 20.88 21.24 20.79 21.51 20.62C22.08 20.27 22.43 19.66 22.44 19C22.45 18.65 22.36 18.3 22.18 18L13.71 3.86C13.54 3.57 13.3 3.33 13 3.17C12.39 2.83 11.62 2.83 11.01 3.17C10.71 3.33 10.47 3.57 10.29 3.86Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div>
                                <p class="fw-semibold mb-1">Warning — this is permanent</p>
                                <p class="mb-0 small">Once you delete your account, all of your data will be permanently removed and cannot be recovered. Please be certain.</p>
                            </div>
                        </div>
                        <div class="form-check mb-5">
                            <input class="form-check-input" type="checkbox" id="deleteAccount" required>
                            <label class="form-check-label" for="deleteAccount">
                                I confirm that I want to permanently delete my account.
                            </label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger d-flex align-items-center gap-2">
                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M14.1 3.44998H1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M10.6371 3.45L10.1592 2.46421C9.84181 1.80938 9.6831 1.48197 9.40931 1.27776C9.34858 1.23247 9.28428 1.19218 9.21703 1.15729C8.91385 1 8.54999 1 7.82228 1C7.07629 1 6.7033 1 6.39509 1.16388C6.32678 1.20021 6.2616 1.24213 6.20022 1.28922C5.92326 1.50169 5.76855 1.84109 5.45913 2.51988L5.03516 3.45" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M6.05078 11.15L6.05078 6.95003" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M9.55078 11.15L9.55078 6.94995" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                Delete My Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
