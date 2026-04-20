{{-- ═══════════════════════════════════════════════════════════════
     views/roles/show.blade.php
════════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('page-title', 'Role Details')
@section('title', $role->name)

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Role Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $role->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if (auth()->user()->hasPermission('roles.edit'))
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M9.89941 15L15.4994 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Edit Role
                </a>
            @endif
            @if (auth()->user()->hasPermission('roles.delete') && !in_array($role->slug, ['admin']))
                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this role?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-label-secondary">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-5">

        {{-- Main Card --}}
        <div class="col-lg-8">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Role Information
                    </h3>
                </div>
                <div class="pure-card-body">

                    {{-- Details Grid --}}
                    <div class="row row-cols-1 row-cols-lg-2 gy-6 mb-6">
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Role Name</p>
                            <p class="fw-medium mb-0 text-primary">{{ $role->name }}</p>
                        </div>
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Slug</p>
                            <p class="fw-medium mb-0"><code>{{ $role->slug }}</code></p>
                        </div>
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Priority</p>
                            <span class="badge badge-label-secondary">{{ $role->priority }}</span>
                        </div>
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Total Permissions</p>
                            <span class="badge badge-label-success">{{ $role->permissions->count() }}</span>
                        </div>
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Created</p>
                            <p class="fw-medium mb-0">{{ $role->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col">
                            <p class="text-custom-paragraph mb-1">Last Updated</p>
                            <p class="fw-medium mb-0">{{ $role->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if ($role->description)
                            <div class="col-12">
                                <p class="text-custom-paragraph mb-1">Description</p>
                                <p class="fw-medium mb-0">{{ $role->description }}</p>
                            </div>
                        @endif
                    </div>

                    <hr>

                    {{-- Permissions --}}
                    <h6 class="fw-semibold mb-5 mt-6">Permissions</h6>
                    @forelse($role->permissions->groupBy('module') as $module => $permissions)
                        <div class="mb-5">
                            <p class="text-custom-paragraph fw-medium mb-2">{{ ucfirst($module) }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($permissions as $permission)
                                    <span class="badge badge-label-secondary">
                                        {{ $permission->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-custom-body">No permissions assigned.</p>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- Sidebar: Users --}}
        <div class="col-lg-4">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title m-0">Users with this Role</h3>
                </div>
                <div class="pure-card-body">
                    <div class="mb-5">
                        <span class="display-5 fw-bold text-primary">{{ $role->users->count() }}</span>
                        <p class="text-custom-paragraph mb-0">assigned users</p>
                    </div>

                    @if ($role->users->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($role->users->take(5) as $user)
                                <li class="list-group-item px-0 py-3 border-top">
                                    <a href="{{ route('users.show', $user) }}"
                                        class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $user->name }}</p>
                                            <small class="text-custom-body">{{ $user->email }}</small>
                                        </div>
                                        @if ($user->is_active)
                                            <span class="badge badge-label-success ms-2">Active</span>
                                        @else
                                            <span class="badge badge-label-secondary ms-2">Inactive</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @if ($role->users->count() > 5)
                            <div class="mt-4">
                                <a href="{{ route('users.index', ['role_id' => $role->id]) }}"
                                    class="btn btn-primary btn-sm w-100">
                                    View All {{ $role->users->count() }} Users
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-custom-body mb-0">No users assigned to this role yet.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
