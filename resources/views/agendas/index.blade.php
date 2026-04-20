{{-- views/agendas/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Agendas')
@section('title', 'Agenda Management')

@section('content')
    {{-- Replace the existing page-header div in agendas/index.blade.php --}}
    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Agenda Management</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Agenda Management</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            {{-- Export PDF --}}
            <a href="{{ route('agendas.export.pdf', request()->query()) }}"
                class="btn btn-outline-danger d-flex align-items-center gap-2" title="Export to PDF" target="_blank">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14 2V8H20M9 13H15M9 17H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                PDF
            </a>

            {{-- Print --}}
            <a href="{{ route('agendas.export.print', request()->query()) }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2" title="Print" target="_blank">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 9V2H18V9M6 18H4C3 18 2 17 2 16V11C2 10 3 9 4 9H20C21 9 22 10 22 11V16C22 17 21 18 20 18H18M6 14H18V22H6V14Z"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Print
            </a>

            {{-- Create --}}
            <a href="{{ route('agendas.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Create Agenda
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Stat Cards ──────────────────────────────────────────────── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 row-cols-xxl-5 g-3 mb-6">

        {{-- Total --}}
        <div class="col">
            <div class="card shadow-custom rounded-custom">
                <div class="card-body p-6 position-relative z-1">
                    <span class="fz-13px fw-medium d-block mb-1">Total Agendas</span>
                    <h3 class="h6 mb-1 fs-5 mb-5">{{ $stats['total'] }}</h3>
                    <div class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                        <span
                            class="bg-label-primary px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                            All
                        </span>
                        <span class="fz-12px fw-medium">Agendas</span>
                    </div>
                    <div class="position-absolute top-6 end-6 z-m-1">
                        <div class="btn-icon bg-primary rounded-pill btn-lg mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 6H16M8 10H16M8 14H12M5 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Draft --}}
        <div class="col">
            <div class="card shadow-custom rounded-custom">
                <div class="card-body p-6 position-relative z-1">
                    <span class="fz-13px fw-medium d-block mb-1">Draft</span>
                    <h3 class="h6 mb-1 fs-5 mb-5">{{ $stats['draft'] }}</h3>
                    <div class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                        <span
                            class="bg-label-secondary px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                            Saved
                        </span>
                        <span class="fz-12px fw-medium">Not submitted</span>
                    </div>
                    <div class="position-absolute top-6 end-6 z-m-1">
                        <div class="btn-icon bg-secondary rounded-pill btn-lg mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Review --}}
        <div class="col">
            <div class="card shadow-custom rounded-custom">
                <div class="card-body p-6 position-relative z-1">
                    <span class="fz-13px fw-medium d-block mb-1">Pending Review</span>
                    <h3 class="h6 mb-1 fs-5 mb-5">{{ $stats['pending_review'] }}</h3>
                    <div class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                        <span
                            class="bg-label-warning px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M6 3.5V6.5L7.5 8" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            Waiting
                        </span>
                        <span class="fz-12px fw-medium">Needs review</span>
                    </div>
                    <div class="position-absolute top-6 end-6 z-m-1">
                        <div class="btn-icon bg-warning rounded-pill btn-lg mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 8V12M12 16H12.01M10.29 3.86L1.82 18C1.64537 18.3024 1.55296 18.6453 1.55198 18.9945C1.55099 19.3437 1.64147 19.6871 1.81442 19.9905C1.98736 20.2939 2.23672 20.5468 2.53773 20.7239C2.83873 20.901 3.18082 20.9962 3.53 21H20.47C20.8192 20.9962 21.1613 20.901 21.4623 20.7239C21.7633 20.5468 22.0126 20.2939 22.1856 19.9905C22.3585 19.6871 22.449 19.3437 22.448 18.9945C22.447 18.6453 22.3546 18.3024 22.18 18L13.71 3.86C13.5317 3.56611 13.2807 3.32312 12.9812 3.15448C12.6817 2.98585 12.3437 2.89725 12 2.89725C11.6563 2.89725 11.3183 2.98585 11.0188 3.15448C10.7193 3.32312 10.4683 3.56611 10.29 3.86Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="col">
            <div class="card shadow-custom rounded-custom">
                <div class="card-body p-6 position-relative z-1">
                    <span class="fz-13px fw-medium d-block mb-1">Approved</span>
                    <h3 class="h6 mb-1 fs-5 mb-5">{{ $stats['approved'] }}</h3>
                    <div class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                        <span
                            class="bg-label-success px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 11V1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M1 6L6 1L11 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Done
                        </span>
                        <span class="fz-12px fw-medium">This period</span>
                    </div>
                    <div class="position-absolute top-6 end-6 z-m-1">
                        <div class="btn-icon bg-success rounded-pill btn-lg mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rejected --}}
        <div class="col">
            <div class="card shadow-custom rounded-custom">
                <div class="card-body p-6 position-relative z-1">
                    <span class="fz-13px fw-medium d-block mb-1">Rejected</span>
                    <h3 class="h6 mb-1 fs-5 mb-5">{{ $stats['rejected'] }}</h3>
                    <div class="p-px-2 pr-px-10 border rounded-pill d-inline-flex align-items-center gap-2">
                        <span
                            class="bg-label-danger px-px-8 py-px-1 rounded-pill d-inline-flex align-items-center gap-1 fz-12px fw-medium">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.75 0.75L5.75 10.75" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.75 5.75L5.75 10.75L0.75 5.75" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Failed
                        </span>
                        <span class="fz-12px fw-medium">This period</span>
                    </div>
                    <div class="position-absolute top-6 end-6 z-m-1">
                        <div class="btn-icon bg-danger rounded-pill btn-lg mb-3">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 9L9 15M9 9L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Filters ──────────────────────────────────────────────────── --}}
    <div class="card shadow-custom rounded-custom mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('agendas.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-medium">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        value="{{ request('search') }}" placeholder="Title, agenda number...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['draft', 'pending_review', 'approved', 'rejected', 'revised'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priority</option>
                        @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                            <option value="{{ $p }}" @selected(request('priority') === $p)>
                                {{ ucfirst($p) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach (['financial', 'operational', 'strategic', 'legal', 'hr', 'it', 'marketing', 'governance', 'other'] as $c)
                            <option value="{{ $c }}" @selected(request('category') === $c)>
                                {{ ucfirst($c) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                    <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────────────── --}}
    <div class="card shadow-custom rounded-custom">
        <div class="table-responsive overflow-hidden pt-0">
            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Agenda #</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Meeting Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendas as $agenda)
                        <tr>
                            <td></td>
                            <td>
                                <code class="text-custom-body fw-medium">{{ $agenda->agenda_number }}</code>
                            </td>
                            <td>
                                <a href="{{ route('agendas.show', $agenda) }}"
                                    class="fw-medium text-body text-decoration-none">
                                    {{ Str::limit($agenda->title, 50) }}
                                </a>
                                @if ($agenda->version > 1)
                                    <span class="badge badge-label-info ms-1 small">v{{ $agenda->version }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $agenda->getPriorityBadgeClass() }}">
                                    {{ ucfirst($agenda->priority) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="text-custom-body">{{ $agenda->category ? ucfirst($agenda->category) : '—' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $agenda->getStatusBadgeClass() }}">
                                    {{ ucfirst(str_replace('_', ' ', $agenda->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-custom-body small">{{ $agenda->creator?->name ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="text-custom-body small">
                                    {{ $agenda->meeting_date?->format('d M Y') ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('agendas.show', $agenda) }}"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill" title="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                            <circle cx="12" cy="12" r="3" stroke="currentColor"
                                                stroke-width="1.5" />
                                        </svg>
                                    </a>

                                    @if ($agenda->canBeEdited() && (auth()->id() === $agenda->created_by || auth()->user()->hasRole('admin')))
                                        <a href="{{ route('agendas.edit', $agenda) }}"
                                            class="btn btn-sm btn-icon btn-icon-secondary rounded-pill" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 17 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linejoin="round" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if (!$agenda->isApproved() && (auth()->id() === $agenda->created_by || auth()->user()->hasRole('admin')))
                                        <form action="{{ route('agendas.destroy', $agenda) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this agenda?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                                title="Delete">
                                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M13.0508 3.44995L12.617 10.4675C12.5061 12.2605 12.4507 13.1569 12.0013 13.8015C11.7791 14.1201 11.493 14.3891 11.1613 14.5912C10.4903 15 9.59207 15 7.7957 15C5.99696 15 5.09759 15 4.42612 14.5904C4.09414 14.3879 3.80798 14.1185 3.58586 13.7993C3.13659 13.1538 3.0824 12.256 2.97401 10.4606L2.55078 3.44995"
                                                        stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                    <path d="M14.1 3.44998H1.5" stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                    <path
                                                        d="M10.6371 3.45L10.1592 2.46421C9.84181 1.80938 9.6831 1.48197 9.40931 1.27776C9.34858 1.23247 9.28428 1.19218 9.21703 1.15729C8.91385 1 8.54999 1 7.82228 1C7.07629 1 6.7033 1 6.39509 1.16388C6.32678 1.20021 6.2616 1.24213 6.20022 1.28922C5.92326 1.50169 5.76855 1.84109 5.45913 2.51988L5.03516 3.45"
                                                        stroke="currentColor" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-custom-paragraph">
                                No agendas found. <a href="{{ route('agendas.create') }}">Create one</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
