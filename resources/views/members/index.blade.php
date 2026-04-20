@extends('layouts.app')

@section('page-title', 'Members')
@section('title', 'Member Management')

@section('content')
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">All Members</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Members</li>
                </ol>
            </nav>
        </div>
        @if (auth()->user()->hasAnyRole(['admin', 'md']))
            <a href="{{ route('members.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Add Member
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Bar --}}
    <div class="pure-card rounded-custom card-bg shadow-custom mb-4">
        <div class="pure-card-body py-3">
            <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm" style="max-width:220px"
                    placeholder="Name, email, ID">

                <select name="department" class="form-select form-select-sm" style="max-width:180px">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ $dept }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-sm" style="max-width:140px">
                    <option value="">All Status</option>
                    <option value="active"   @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <button class="btn btn-sm btn-primary">Filter</button>
                @if (request()->hasAny(['search','department','status']))
                    <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="pure-card rounded-custom card-bg shadow-custom">
        <div class="pure-card-header">
            <h3 class="pure-card-title m-0">
                Members
                <span class="badge badge-label-primary ms-2">{{ $members->total() }}</span>
            </h3>
        </div>
        <div class="pure-card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="text-custom-paragraph fw-medium">#</th>
                            <th class="text-custom-paragraph fw-medium">Member</th>
                            <th class="text-custom-paragraph fw-medium">Employee ID</th>
                            <th class="text-custom-paragraph fw-medium">Designation</th>
                            <th class="text-custom-paragraph fw-medium">Department</th>
                            <th class="text-custom-paragraph fw-medium">Contact</th>
                            <th class="text-custom-paragraph fw-medium">Status</th>
                            <th class="text-custom-paragraph fw-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr>
                                <td class="text-custom-body small">{{ $members->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $member->photo_url }}"
                                            class="rounded-circle object-fit-cover flex-shrink-0"
                                            width="38" height="38" alt="{{ $member->name }}">
                                        <div>
                                            <div class="fw-semibold small">{{ $member->name }}</div>
                                            <div class="text-custom-body" style="font-size:.75rem">{{ $member->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><code class="small">{{ $member->employee_id ?? '—' }}</code></td>
                                <td class="small">{{ $member->designation ?? '—' }}</td>
                                <td><span class="badge badge-label-info">{{ $member->department ?? '—' }}</span></td>
                                <td class="small text-custom-body">{{ $member->phone ?? '—' }}</td>
                                <td>
                                    @if ($member->status === 'active')
                                        <span class="badge badge-label-success">Active</span>
                                    @else
                                        <span class="badge badge-label-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        {{-- View --}}
                                        <a href="{{ route('members.show', $member) }}"
                                            class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                            data-bs-toggle="tooltip" title="View">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                                        </a>

                                        {{-- Edit --}}
                                        @if (auth()->user()->hasRole('admin'))
                                            <a href="{{ route('members.edit', $member) }}"
                                                class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <svg width="16" height="16" viewBox="0 0 17 16" fill="none"><path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                            </a>

                                            {{-- Toggle Status --}}
                                            @if ($member->status === 'active')
                                                <form action="{{ route('members.deactivate', $member) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                        data-bs-toggle="tooltip" title="Deactivate"
                                                        onclick="return confirm('Disable?')">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M9 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('members.activate', $member) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                        data-bs-toggle="tooltip" title="Activate">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline"
                                                onsubmit="return confirm('Delete this member?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                    data-bs-toggle="tooltip" title="Delete">
                                                    <svg width="15" height="16" viewBox="0 0 15 16" fill="none"><path d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14.1 3.44998H1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M10.6371 3.45L10.1592 2.46421C9.84181 1.80938 9.6831 1.48197 9.40931 1.27776C9.34858 1.23247 9.28428 1.19218 9.21703 1.15729C8.91385 1 8.54999 1 7.82228 1C7.07629 1 6.7033 1 6.39509 1.16388C6.32678 1.20021 6.2616 1.24213 6.20022 1.28922C5.92326 1.50169 5.76855 1.84109 5.45913 2.51988L5.03516 3.45" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <p class="text-custom-paragraph mb-2">No members found</p>
                                    @if (auth()->user()->hasRole('admin'))
                                        <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">Add First Member</a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $members->links() }}
            </div>
        </div>
    </div>
@endsection
