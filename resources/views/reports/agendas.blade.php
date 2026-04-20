@extends('layouts.app')
@section('title', 'Agenda & Implementation Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Agenda & Implementation Report</h4>
        <p class="text-muted mb-0">Agenda tracking by status, priority and approval workflow</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">Export PDF</a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.agendas') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['draft','pending_review','approved','rejected','revised'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Priority</label>
                <select name="priority" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['low','medium','high','urgent'] as $p)
                        <option value="{{ $p }}" @selected(request('priority') === $p)>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Meeting Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Meeting Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('reports.agendas') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Total',          'value' => $summary['total'],          'color' => 'primary'],
        ['label' => 'Draft',          'value' => $summary['draft'],          'color' => 'secondary'],
        ['label' => 'Pending Review', 'value' => $summary['pending_review'], 'color' => 'warning'],
        ['label' => 'Approved',       'value' => $summary['approved'],       'color' => 'success'],
        ['label' => 'Rejected',       'value' => $summary['rejected'],       'color' => 'danger'],
    ] as $item)
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-{{ $item['color'] }}">{{ $item['value'] }}</div>
            <div class="text-muted small">{{ $item['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Agenda No.</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Category</th>
                        <th>Meeting Date</th>
                        <th>Assigned To</th>
                        <th>Approved By</th>
                        <th>Docs</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendas as $i => $agenda)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <a href="{{ route('agendas.show', $agenda) }}"
                               class="text-decoration-none">
                                <code class="small">{{ $agenda->agenda_number }}</code>
                            </a>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ Str::limit($agenda->title, 40) }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $agenda->getPriorityBadgeClass() }}">
                                {{ ucfirst($agenda->priority) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ ucfirst($agenda->category ?? '—') }}</td>
                        <td class="small text-nowrap">{{ $agenda->meeting_date?->format('d M Y') ?? '—' }}</td>
                        <td class="small text-muted">{{ $agenda->assignee?->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $agenda->approver?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $agenda->documents_count }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $agenda->getStatusBadgeClass() }}">
                                {{ ucfirst(str_replace('_', ' ', $agenda->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">No agendas found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
