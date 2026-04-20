{{-- ═══════════════════════════════════════════════════════════════
     views/roles/index.blade.php
════════════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('page-title', 'Roles')
@section('title', 'Roles')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Role Management</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
        @if (auth()->user()->hasPermission('roles.create'))
            <div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Create Role
                </a>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="pure-card rounded-custom card-bg shadow-custom">
                <div class="pure-card-header">
                    <h3 class="pure-card-title d-flex align-items-center gap-2 m-0">
                        <span class="text-primary d-flex align-items-center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        All Roles
                    </h3>
                </div>
                <div class="pure-card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Name</th>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Priority</th>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Description</th>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Permissions</th>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Users</th>
                                    <th scope="col" class="text-custom-paragraph fw-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>
                                            <span class="text-primary fw-medium">{{ $role->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-label-secondary">{{ $role->priority }}</span>
                                        </td>
                                        <td>
                                            <span class="text-custom-body">{{ $role->description ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @forelse($role->permissions->take(3) as $permission)
                                                <span class="badge badge-label-secondary me-1 mb-1">{{ $permission->name }}</span>
                                            @empty
                                                <span class="text-custom-body">No permissions</span>
                                            @endforelse
                                            @if ($role->permissions->count() > 3)
                                                <span class="badge badge-label-secondary">+{{ $role->permissions->count() - 3 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-label-success">{{ $role->users_count ?? $role->users->count() }} users</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{-- View --}}
                                                <a href="{{ route('roles.show', $role) }}"
                                                    class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                                    </svg>
                                                </a>

                                                {{-- Edit --}}
                                                @if (auth()->user()->hasPermission('roles.edit'))
                                                    <a href="{{ route('roles.edit', $role) }}"
                                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                        <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                            <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                            <path d="M9.89941 15L15.4994 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                {{-- Delete --}}
                                                @if (auth()->user()->hasPermission('roles.delete') && !in_array($role->slug, ['admin']))
                                                    <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                        style="display:inline;"
                                                        onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                            <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M14.1 3.44998H1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M10.6371 3.45L10.1592 2.46421C9.84181 1.80938 9.6831 1.48197 9.40931 1.27776C9.34858 1.23247 9.28428 1.19218 9.21703 1.15729C8.91385 1 8.54999 1 7.82228 1C7.07629 1 6.7033 1 6.39509 1.16388C6.32678 1.20021 6.2616 1.24213 6.20022 1.28922C5.92326 1.50169 5.76855 1.84109 5.45913 2.51988L5.03516 3.45" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M6.05078 11.15L6.05078 6.95003" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M9.55078 11.15L9.55078 6.94995" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="text-custom-paragraph mb-2">No roles found</p>
                                            @if (auth()->user()->hasPermission('roles.create'))
                                                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm mt-2">
                                                    Create First Role
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($roles instanceof \Illuminate\Pagination\Paginator)
                        <div class="d-flex justify-content-end mt-4">
                            {{ $roles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
