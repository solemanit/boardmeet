{{-- views/circulars/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Circulars')
@section('page-title', 'Circulars')

@section('content')

    <div class="page-header pb-7 d-flex justify-content-between align-items-center gap-6 flex-wrap">
        <div>
            <h2 class="fw-semibold fs-7">Circulars & Notices</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Circulars</li>
                </ol>
            </nav>
        </div>
        @if (auth()->user()->hasRole('admin'))
            <a href="{{ route('circulars.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1 8H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                New Circular
            </a>
        @endif
    </div>

    @include('layouts.partials.alerts')

    {{-- Stats Row --}}
    <div class="row g-4 mb-6">
        @foreach ([
            ['Total',     $stats['total'],     'primary'],
            ['Published', $stats['published'], 'success'],
            ['Draft',     $stats['draft'],     'warning'],
        ] as [$label, $count, $color])
            <div class="col-sm-4">
                <div class="pure-card rounded-custom card-bg shadow-custom p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $color }} bg-opacity-10"
                        style="width:44px;height:44px;">
                        <span class="fw-bold text-{{ $color }}" style="font-size:1.1rem;">{{ $count }}</span>
                    </div>
                    <span class="text-custom-paragraph fw-medium">{{ $label }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="pure-card rounded-custom card-bg shadow-custom">
        <div class="pure-card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h3 class="pure-card-title m-0">All Circulars</h3>

            {{-- Server-side Filter Form --}}
            <form method="GET" action="{{ route('circulars.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control form-control-sm"
                    placeholder="Search title, ref…"
                    style="width:200px;">

                <select name="type" class="form-select form-select-sm" style="width:140px;">
                    <option value="">All Types</option>
                    @foreach (['circular' => 'Circular', 'notice' => 'Notice', 'memo' => 'Memo'] as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('type') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-sm" style="width:130px;">
                    <option value="">All Status</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="draft"     @selected(request('status') === 'draft')>Draft</option>
                </select>

                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>

                @if (request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('circulars.index') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                @endif
            </form>
        </div>

        <div class="pure-card-body">
            <div class="table-responsive overflow-hidden">
                <table id="circulars-table" class="table">
                    <thead>
                        <tr>
                            <th class="text-custom-paragraph fw-medium">Title</th>
                            <th class="text-custom-paragraph fw-medium">Ref No.</th>
                            <th class="text-custom-paragraph fw-medium">Type</th>
                            <th class="text-custom-paragraph fw-medium">Issued</th>
                            <th class="text-custom-paragraph fw-medium">Status</th>
                            <th class="text-custom-paragraph fw-medium">Attachments</th>
                            <th class="text-custom-paragraph fw-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($circulars as $circular)
                            <tr>
                                <td>
                                    <a href="{{ route('circulars.show', $circular) }}"
                                        class="fw-medium text-dark text-decoration-none">
                                        {{ Str::limit($circular->title, 50) }}
                                    </a>
                                </td>

                                <td>
                                    <code class="text-custom-body">{{ $circular->reference_number ?? '—' }}</code>
                                </td>

                                <td>
                                    <span class="badge badge-label-{{ $circular->type_badge_color }}">
                                        {{ ucfirst($circular->type) }}
                                    </span>
                                </td>

                                <td>{{ $circular->issued_date->format('d M, Y') }}</td>

                                <td>
                                    @if ($circular->isPublished())
                                        <span class="badge badge-label-success">Published</span>
                                    @else
                                        <span class="badge badge-label-warning">Draft</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($circular->attachments->isNotEmpty())
                                        <span class="badge badge-label-info">
                                            {{ $circular->attachments->count() }} file(s)
                                        </span>
                                    @else
                                        <span class="text-custom-paragraph">—</span>
                                    @endif
                                </td>

                                <td>
                                    {{-- View --}}
                                    <a href="{{ route('circulars.show', $circular) }}"
                                        class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </a>

                                    @if (auth()->user()->hasRole('admin'))
                                        {{-- Edit --}}
                                        <a href="{{ route('circulars.edit', $circular) }}"
                                            class="btn btn-sm btn-icon btn-icon-secondary rounded-pill"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.9516 2.31982C10.4732 1.75466 10.734 1.47208 11.0112 1.30725C11.6799 0.909531 12.5034 0.897164 13.1833 1.27463C13.465 1.43106 13.7339 1.70568 14.2715 2.25493C14.8092 2.80418 15.078 3.07881 15.2312 3.36665C15.6007 4.06118 15.5886 4.90235 15.1992 5.58549C15.0379 5.86861 14.7613 6.13504 14.208 6.66791L7.62544 13.008C6.57701 14.0178 6.0528 14.5227 5.39764 14.7786C4.74248 15.0345 4.02224 15.0157 2.58176 14.978L2.38576 14.9729C1.94723 14.9614 1.72797 14.9557 1.60051 14.811C1.47305 14.6664 1.49045 14.443 1.52526 13.9963L1.54415 13.7538C1.64211 12.4965 1.69108 11.8678 1.9366 11.3028C2.18211 10.7377 2.6056 10.2788 3.4526 9.36115L9.9516 2.31982Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                <path d="M9.19922 2.39996L14.0992 7.29996" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                                                <path d="M9.89941 15L15.4994 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('circulars.destroy', $circular) }}" method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Delete this circular? This cannot be undone.')">
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Laravel Pagination --}}
            @if ($circulars->hasPages())
                <div class="mt-4">
                    {{ $circulars->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#circulars-table').DataTable({
        responsive:   true,
        ordering:     true,
        searching:    false,  // using server-side filter form
        paging:       false,  // using Laravel pagination
        info:         false,
        lengthChange: false,
        language: {
            emptyTable:
                '<div class="text-center py-5">' +
                    '<p class="text-custom-paragraph mb-2">No circulars found</p>' +
                    @if (auth()->user()->hasRole('admin'))
                    '<a href="{{ route('circulars.create') }}" class="btn btn-primary btn-sm">Create First</a>' +
                    @endif
                '</div>',
        },
        columnDefs: [
            { orderable: false, targets: [5, 6] }, // Attachments + Actions not sortable
        ],
    });
});
</script>
@endpush
