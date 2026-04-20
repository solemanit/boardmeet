{{-- views/users/show.blade.php --}}
@extends('layouts.app')

@section('page-title', 'User Details')
@section('title', $user->name)

@section('content')

    {{-- ── Profile Header ─────────────────────────────────────────────── --}}
    <div class="profile-header card-bg shadow-custom rounded-custom position-relative overflow-hidden mb-6">
        <div class="profile-cover-wrapper">
            <div class="profile-cover-img" style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);"></div>
        </div>

        <div class="profile-header-bottom position-relative d-flex justify-content-between align-items-end mx-8 pb-5 flex-wrap gap-5 z-1">
            <div class="profile-avatar-wrapper d-flex align-items-end gap-3 flex-wrap">
                <div class="profile-avatar flex-shrink-0 d-flex align-items-center justify-content-center bg-primary text-white fw-bold rounded-pill"
                    style="font-size:2rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="profile-info pb-5">
                    <h3 class="h3 fw-semibold mb-1">{{ $user->name }}</h3>
                    <div class="profile-meta">
                        <span>{{ $user->role?->name ?? 'No Role' }}</span>
                        <span>{{ $user->email }}</span>
                        @if ($user->is_active)
                            <span class="badge badge-label-success">Active</span>
                        @else
                            <span class="badge badge-label-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="profile-avatar-buttons d-flex align-items-center pb-5 gap-2">
                @if (auth()->user()->hasPermission('users.edit'))
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary d-flex align-items-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M9.89941 15L15.4994 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Edit User
                    </a>
                @endif
                @if (auth()->user()->hasPermission('users.delete'))
                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-custom-secondary">Delete</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="profile-nav mx-8">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('users.show', $user) }}">Overview</a>
                </li>
                @if (auth()->user()->hasPermission('users.edit'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.edit', $user) }}">Edit</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.activity', $user) }}">Activity Logs</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- ── Body ────────────────────────────────────────────────────────── --}}
    <div class="row">

        {{-- Sticky Sidebar Nav --}}
        <div class="col-md-4">
            <div class="rounded-custom card-bg shadow-custom px-7 py-2 mb-6 position-sticky top-8">
                <nav id="user-show-nav" class="side-navigation flex-column align-items-stretch">
                    <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                        href="#section-user-info">
                        <span>
                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.83203 8C8.76503 8 10.332 6.433 10.332 4.5C10.332 2.567 8.76503 1 6.83203 1C4.89903 1 3.33203 2.567 3.33203 4.5C3.33203 6.433 4.89903 8 6.83203 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path opacity="0.4" d="M13.026 14.9999C13.026 12.2909 10.331 10.0999 7.013 10.0999C3.695 10.0999 1 12.2909 1 14.9999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        User Information
                    </a>
                    <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                        href="#section-security">
                        <span>
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M11.5 14H4.5C2.4 14 1 12.8529 1 10.1765V4.82353C1 2.14706 2.4 1 4.5 1H11.5C13.6 1 15 2.14706 15 4.82353V10.1765C15 12.8529 13.6 14 11.5 14Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.5 5.20587L9.309 7.11764C8.588 7.7447 7.405 7.7447 6.684 7.11764L4.5 5.20587" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Security
                    </a>
                    <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4 border-bottom"
                        href="#section-permissions">
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Permissions
                    </a>
                    <a class="nav-link d-flex align-items-center gap-3 fw-medium py-4"
                        href="#section-activity">
                        <span>
                            <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M13 4.75V12.25C13 15.25 12.25 16 9.25 16H4.75C1.75 16 1 15.25 1 12.25V4.75C1 1.75 1.75 1 4.75 1H9.25C12.25 1 13 1.75 13 4.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.5 3.625H5.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.00234 13.825C7.64437 13.825 8.16484 13.3045 8.16484 12.6625C8.16484 12.0205 7.64437 11.5 7.00234 11.5C6.36031 11.5 5.83984 12.0205 5.83984 12.6625C5.83984 13.3045 6.36031 13.825 7.00234 13.825Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Recent Activity
                    </a>
                </nav>
            </div>
        </div>

        {{-- Scrollspy Content --}}
        <div class="col-md-8">
            <div data-bs-spy="scroll" data-bs-target="#user-show-nav" data-bs-smooth-scroll="true" tabindex="0">

                {{-- ── User Information ──────────────────────────────── --}}
                <div id="section-user-info" class="pure-card rounded-custom card-bg shadow-custom">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                            <span class="text-primary d-flex align-items-center">
                                <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.4763 11.7073C13.6583 11.7073 16.2378 9.08652 16.2378 5.85365C16.2378 2.62077 13.6583 0 10.4763 0C7.29436 0 4.71484 2.62077 4.71484 5.85365C4.71484 9.08652 7.29436 11.7073 10.4763 11.7073Z" fill="currentColor"/>
                                    <path opacity="0.4" d="M10.4744 13.6342C4.70142 13.6342 0 17.5678 0 22.4146C0 22.7424 0.253506 23 0.57615 23H20.3727C20.6954 23 20.9489 22.7424 20.9489 22.4146C20.9489 17.5678 16.2475 13.6342 10.4744 13.6342Z" fill="currentColor"/>
                                    <path d="M21.3452 13.9151C20.3081 12.8615 19.49 13.201 18.7871 13.9151L14.708 18.0596C14.5466 18.2235 14.3968 18.5278 14.3623 18.7503L14.1433 20.3307C14.0627 20.9044 14.4545 21.3024 15.0191 21.2205L16.5747 20.998C16.7936 20.9629 17.1048 20.8107 17.2546 20.6468L21.3337 16.5025C22.0481 15.8 22.3823 14.9688 21.3452 13.9151Z" fill="currentColor"/>
                                </svg>
                            </span>
                            User Information
                        </h3>
                    </div>
                    <div class="pure-card-body">
                        <div class="row row-cols-1 row-cols-lg-2 gy-6">
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Full Name</p>
                                <p class="fw-medium mb-0">{{ $user->name }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Email Address</p>
                                <p class="fw-medium mb-0">{{ $user->email }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Phone</p>
                                <p class="fw-medium mb-0">{{ $user->phone_number ?? '—' }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Role</p>
                                <p class="fw-medium mb-0 text-primary">{{ $user->role?->name ?? 'No Role' }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Status</p>
                                @if ($user->is_active)
                                    <span class="badge badge-label-success">Active</span>
                                @else
                                    <span class="badge badge-label-secondary">Inactive</span>
                                @endif
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Last Login</p>
                                <p class="fw-medium mb-0">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Created</p>
                                <p class="fw-medium mb-0">{{ $user->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col">
                                <p class="text-custom-paragraph mb-1">Last Updated</p>
                                <p class="fw-medium mb-0">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Security ──────────────────────────────────────── --}}
                <div id="section-security" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                            <span class="text-primary d-flex align-items-center">
                                <svg width="23" height="20" viewBox="0 0 23 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.9883 5.71102C21.5654 5.71102 22.8438 4.43257 22.8438 2.85551C22.8438 1.27846 21.5654 0 19.9883 0C18.4113 0 17.1328 1.27846 17.1328 2.85551C17.1328 4.43257 18.4113 5.71102 19.9883 5.71102Z" fill="currentColor"/>
                                    <path opacity="0.4" d="M21.3821 7.20731C22.1017 6.97887 22.8441 7.53856 22.8441 8.30384V14.289C22.8441 18.2867 20.5597 20 17.1331 20H5.71102C2.28441 20 0 18.2867 0 14.289V6.29354C0 2.29583 2.28441 0.58252 5.71102 0.58252H14.4032C15.1456 0.58252 15.6482 1.26785 15.5111 1.98744C15.3741 2.66135 15.3969 3.38093 15.6025 4.12336C16.0251 5.65392 17.2701 6.87607 18.8007 7.27584C19.703 7.50428 20.5825 7.4586 21.3821 7.20731Z" fill="currentColor"/>
                                    <path d="M11.4223 11.2736C10.4629 11.2736 9.49201 10.9766 8.74958 10.3713L5.17447 7.51576C4.80897 7.21878 4.74044 6.68194 5.03741 6.31644C5.33439 5.95093 5.87121 5.88241 6.23671 6.17938L9.81182 9.03489C10.6799 9.73164 12.1533 9.73164 13.0214 9.03489L14.3692 7.96122C14.7347 7.66424 15.283 7.72135 15.5685 8.09828C15.8655 8.46378 15.8084 9.01204 15.4315 9.29759L14.0837 10.3713C13.3526 10.9766 12.3818 11.2736 11.4223 11.2736Z" fill="currentColor"/>
                                </svg>
                            </span>
                            Security
                        </h3>
                    </div>
                    <div class="pure-card-body">

                        {{-- Account Status --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-6 pb-8 border-bottom">
                            <div>
                                <p class="text-custom-paragraph m-0">Account Status</p>
                                <p class="m-0 fz-16px fw-medium">
                                    @if ($user->is_active) Active @else Inactive @endif
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                @if ($user->is_active && auth()->user()->hasPermission('users.edit'))
                                    <form action="{{ route('users.deactivate', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-custom-secondary">Deactivate</button>
                                    </form>
                                @endif
                                @if (!$user->is_active && auth()->user()->hasPermission('users.edit'))
                                    <form action="{{ route('users.activate', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Activate</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-6 pt-8 pb-8 border-bottom">
                            <div>
                                <p class="text-custom-paragraph m-0">Password</p>
                                <p class="m-0 fz-16px fw-medium">*******************</p>
                            </div>
                            @if (auth()->user()->hasPermission('users.reset_password'))
                                <div>
                                    <button type="button" class="btn btn-custom-secondary"
                                        data-bs-toggle="collapse" data-bs-target="#collapseResetPassword"
                                        aria-expanded="false" aria-controls="collapseResetPassword">
                                        Reset Password
                                    </button>
                                </div>
                            @endif
                        </div>
                        @if (auth()->user()->hasPermission('users.reset_password'))
                            <div class="collapse" id="collapseResetPassword">
                                <div class="py-7 border-bottom">
                                    <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                        @csrf
                                        <div class="row row-cols-1 row-cols-lg-2 gy-6">
                                            <div class="col">
                                                <label for="password" class="form-label">New Password</label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="New Password">
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                <input type="password" name="password_confirmation" id="password_confirmation"
                                                    class="form-control" placeholder="Confirm Password">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-end gap-3 mt-7">
                                            <button type="button" class="btn btn-custom-secondary"
                                                data-bs-toggle="collapse" data-bs-target="#collapseResetPassword">
                                                Cancel
                                            </button>
                                            <button type="submit" class="btn btn-primary">Update Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- Password Last Changed --}}
                        <div class="pt-8">
                            <p class="text-custom-paragraph m-0">Last Password Change</p>
                            <p class="m-0 fz-16px fw-medium">
                                {{ $user->last_password_changed_at?->format('M d, Y H:i') ?? 'Never' }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- ── Permissions ───────────────────────────────────── --}}
                <div id="section-permissions" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                            <span class="text-primary d-flex align-items-center">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            Permissions
                        </h3>
                    </div>
                    <div class="pure-card-body">
                        @forelse($user->getPermissions()->groupBy('module') as $module => $permissions)
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-6 pb-7 border-bottom mb-7">
                                <div>
                                    <p class="text-custom-paragraph mb-1">{{ ucfirst($module) }}</p>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach ($permissions as $permission)
                                            <span class="badge badge-label-secondary">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-custom-body m-0">No permissions assigned.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ── Recent Activity ───────────────────────────────── --}}
                <div id="section-activity" class="pure-card rounded-custom card-bg shadow-custom mt-6">
                    <div class="pure-card-header">
                        <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                            <span class="text-primary d-flex align-items-center">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M23.988 5.2973V13.1051H0V5.2973C0 2.37838 2.37838 0 5.2973 0H18.6907C21.6096 0 23.988 2.37838 23.988 5.2973Z" fill="currentColor"/>
                                    <path d="M0 13.1172V13.3574C0 16.2884 2.37838 18.6547 5.2973 18.6547H9.90991C10.5706 18.6547 11.1111 19.1953 11.1111 19.8559V21.0211C11.1111 21.6818 10.5706 22.2223 9.90991 22.2223H7.003C6.51051 22.2223 6.1021 22.6307 6.1021 23.1232C6.1021 23.6157 6.4985 24.0241 7.003 24.0241H17.033C17.5255 24.0241 17.9339 23.6157 17.9339 23.1232C17.9339 22.6307 17.5255 22.2223 17.033 22.2223H14.1261C13.4655 22.2223 12.9249 21.6818 12.9249 21.0211V19.8559C12.9249 19.1953 13.4655 18.6547 14.1261 18.6547H18.7027C21.6336 18.6547 24 16.2763 24 13.3574V13.1172H0Z" fill="currentColor"/>
                                </svg>
                            </span>
                            Recent Activity
                        </h3>
                    </div>
                    <div class="pure-card-body">
                        @if ($user->activityLogs->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-custom-paragraph fw-medium">Action</th>
                                            <th scope="col" class="text-custom-paragraph fw-medium">IP Address</th>
                                            <th scope="col" class="text-custom-paragraph fw-medium">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->activityLogs->take(10) as $log)
                                            <tr>
                                                <td>
                                                    <span class="fw-medium text-custom-body">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-custom-body">{{ $log->ip_address }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-custom-body">{{ $log->created_at->diffForHumans() }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('users.activity', $user) }}" class="btn btn-primary btn-sm">
                                    View All Activities
                                </a>
                            </div>
                        @else
                            <p class="text-custom-body m-0">No activity logs found.</p>
                        @endif
                    </div>
                </div>

            </div>{{-- /scrollspy --}}
        </div>
    </div>
@endsection
