{{--
    resources/views/layouts/partials/sidebar.blade.php

    Role helpers (custom RBAC):
      auth()->user()->hasRole('admin')
      auth()->user()->hasAnyRole(['admin', 'md'])
      auth()->user()->hasAnyPermission(['users.view', ...])
      auth()->user()->hasPermission('users.create')
--}}

@php
    $activeCls   = fn(string|array $r): string => request()->routeIs(is_array($r) ? $r : [$r]) ? ' active' : '';
    $dropdownCls = fn(string|array $r): string => request()->routeIs(is_array($r) ? $r : [$r]) ? ' active open' : '';
    $authUser    = auth()->user();
@endphp

<div id="app-sidebar" class="app-sidebar overflow-hidden">
<div class="app-sidebar-wrapper">

    {{-- ── Logo ─────────────────────────────────────────────────── --}}
    <div class="app-sidebar-header d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard.index') }}" class="app-sidebar-logo">
            <img class="app-main-logo logo-black" width="230" src="{{ asset('assets/img/logo/logo.png') }}" alt="BoardMeet">
            <img class="app-main-logo logo-white d-none" width="230" src="{{ asset('assets/img/logo/logo-white.png') }}" alt="BoardMeet">
        </a>
        <button type="button" class="app-sidebar-close-btn app-sidebar-mobile-close d-xl-none">
            <svg width="20" height="12" viewBox="0 0 20 12" fill="none">
                <path d="M10.6923 10.2857L6.53846 6M6.53846 6L10.6923 1.71429M6.53846 6L19 6M1 11L1 1"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    {{-- ── Menu ─────────────────────────────────────────────────── --}}
    <div id="app-sidebar-menu" class="app-sidebar-menu">
    <ul>

        {{-- ══════════════════════════════════════════════════════
             GENERAL
        ══════════════════════════════════════════════════════ --}}
        <li class="app-sidebar-menu-heading">
            <span><span class="app-sidebar-menu-heading-line"></span>GENERAL</span>
        </li>

        {{-- Dashboard --}}
        <li class="app-sidebar-menu-item{{ $activeCls('dashboard.index') }}">
            <a href="{{ route('dashboard.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <rect x="0.75"  y="0.75"   width="6.00021" height="7.5" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="0.75"  y="11.2499" width="6.00021" height="4.5" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="9.74976" y="8.25"  width="6.00021" height="7.5" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="9.74976" y="0.75"  width="6.00021" height="4.5" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Dashboard</span>
            </a>
        </li>

        {{-- Profile --}}
        <li class="app-sidebar-menu-item{{ $activeCls('profile.index') }}">
            <a href="{{ route('profile.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <circle cx="8.5" cy="5.5" r="3.25" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M1.75 15.25C1.75 12.3505 4.09 10 7 10H10C12.91 10 15.25 12.3505 15.25 15.25"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Profile</span>
            </a>
        </li>

        {{-- Activity Logs --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['activity.index', 'activity.logs']) }}">
            <a href="{{ route('activity.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M2.125 8.5H4.25L6.375 3.1875L8.5 13.8125L10.625 6.375L12.75 8.5H14.875"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Activity Logs</span>
            </a>
        </li>

        {{-- Circulars --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['circulars.index', 'circulars.create']) }}">
            <a href="{{ route('circulars.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8 13H16M8 17H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Circulars</span>
            </a>
        </li>

        {{-- ══════════════════════════════════════════════════════
             MANAGEMENT  (admin / md / permission holders)
        ══════════════════════════════════════════════════════ --}}
        @if ($authUser->hasAnyRole(['admin', 'md']) ||
             $authUser->hasAnyPermission(['users.view','users.create','agendas.view','agendas.create','meetings.view','meetings.create']))

        <li class="app-sidebar-menu-heading">
            <span><span class="app-sidebar-menu-heading-line"></span>MANAGEMENT</span>
        </li>

        {{-- Meetings --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['meetings.index', 'meetings.create']) }}">
            <a href="{{ route('meetings.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <rect x="1.0625" y="2.125" width="14.875" height="13.25" rx="1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></rect>
                        <path d="M1.0625 6.375H15.9375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M5.3125 1.0625V3.1875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M11.6875 1.0625V3.1875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M4.78125 9.5625H7.4375M4.78125 12.2188H9.5625" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Meetings</span>
            </a>
        </li>

        {{-- Members --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['members.index', 'members.create']) }}">
            <a href="{{ route('members.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2 21C2 17.134 5.13401 14 9 14C12.866 14 16 17.134 16 21"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M19 8V14M16 11H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Members</span>
            </a>
        </li>

        {{-- Users --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['users.index', 'users.create']) }}">
            <a href="{{ route('users.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <circle cx="6.375" cy="5.5" r="3.25" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M0.75 14.875C0.75 12.1826 2.93261 10 5.625 10H7.125C9.81739 10 12 12.1826 12 14.875"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M11.5 7.75C12.7426 7.75 13.75 6.74264 13.75 5.5C13.75 4.25736 12.7426 3.25 11.5 3.25"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M13 10.25C14.8135 10.5636 16.25 11.9317 16.25 14.875"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Users</span>
            </a>
        </li>

        {{-- Reports --}}
        <li class="app-sidebar-menu-item{{ $activeCls(['reports.index', 'reports.create']) }}">
            <a href="{{ route('reports.index') }}" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M14 2V8H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M8 12H16M8 16H13M8 8H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M12 16L14 18L18 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Reports</span>
            </a>
        </li>

        @endif {{-- end MANAGEMENT --}}

        {{-- ══════════════════════════════════════════════════════
             ACCESS CONTROL  (admin only)
        ══════════════════════════════════════════════════════ --}}
        @if ($authUser->hasRole('admin'))

        <li class="app-sidebar-menu-heading">
            <span><span class="app-sidebar-menu-heading-line"></span>ACCESS CONTROL</span>
        </li>

        {{-- Roles --}}
        <li class="app-sidebar-menu-item has-dropdown{{ $dropdownCls(['roles.*']) }}">
            <a href="javascript:void(0);" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M14.4375 7.4375L9.5625 2.5625L2.5625 9.5625L7.4375 14.4375L14.4375 7.4375Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.6875 4.3125L12.6875 1.3125L15.6875 2.3125"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2.5625 9.5625L1.0625 15.9375L7.4375 14.4375"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Roles</span>
                <span class="menu-arrow flex-shrink-0 d-flex align-items-center justify-content-center">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none">
                        <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>
            <ul class="app-sidebar-submenu">
                <li class="app-sidebar-menu-item{{ $activeCls('roles.index') }}">
                    <a href="{{ route('roles.index') }}" class="menu-link d-flex align-items-center">
                        <span class="menu-title flex-grow-1">All Roles</span>
                    </a>
                </li>
                <li class="app-sidebar-menu-item{{ $activeCls('roles.create') }}">
                    <a href="{{ route('roles.create') }}" class="menu-link d-flex align-items-center">
                        <span class="menu-title flex-grow-1">Add Role</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Permissions --}}
        <li class="app-sidebar-menu-item has-dropdown{{ $dropdownCls(['permissions.*']) }}">
            <a href="javascript:void(0);" class="menu-link d-flex align-items-center">
                <span class="menu-icon flex-shrink-0">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M8.5 1.0625L10.7292 5.57396L15.7188 6.30229L12.1094 9.82188L12.9583 14.7917L8.5 12.4479L4.04167 14.7917L4.89063 9.82188L1.28125 6.30229L6.27083 5.57396L8.5 1.0625Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="menu-title flex-grow-1">Permissions</span>
                <span class="menu-arrow flex-shrink-0 d-flex align-items-center justify-content-center">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none">
                        <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>
            <ul class="app-sidebar-submenu">
                <li class="app-sidebar-menu-item{{ $activeCls('permissions.index') }}">
                    <a href="{{ route('permissions.index') }}" class="menu-link d-flex align-items-center">
                        <span class="menu-title flex-grow-1">All Permissions</span>
                    </a>
                </li>
                <li class="app-sidebar-menu-item{{ $activeCls('permissions.create') }}">
                    <a href="{{ route('permissions.create') }}" class="menu-link d-flex align-items-center">
                        <span class="menu-title flex-grow-1">Add Permission</span>
                    </a>
                </li>
            </ul>
        </li>

        @endif {{-- end ACCESS CONTROL --}}

    </ul>
    </div>

    {{-- ── Footer / Logout ──────────────────────────────────────── --}}
    <div class="app-sidebar-footer p-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white fw-bold"
                style="width:38px;height:38px;font-size:1rem;">
                {{ strtoupper(substr($authUser->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="fw-semibold text-truncate small">{{ $authUser->name }}</div>
                <div class="text-truncate" style="font-size:.75rem;opacity:.65;">
                    {{ $authUser->role?->name ?? 'No Role' }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                <svg width="15" height="15" viewBox="0 0 17 17" fill="none">
                    <path d="M6.375 14.875H3.1875C2.32706 14.875 1.625 14.1729 1.625 13.3125V3.6875C1.625 2.82706 2.32706 2.125 3.1875 2.125H6.375"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11.3333 11.6875L15.375 8.5L11.3333 5.3125"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.375 8.5H6.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </button>
        </form>
    </div>

</div>
</div>
