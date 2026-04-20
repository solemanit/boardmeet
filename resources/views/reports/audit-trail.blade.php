@extends('layouts.app')
@section('title', 'Audit Trail')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Audit Trail</h4>
        <p class="text-muted mb-0">Complete user activity log</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
           class="btn btn-sm btn-danger">
            Export PDF (max 500)
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.audit-trail') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Action / Keyword</label>
                <input type="text" name="action" class="form-control form-control-sm"
                    placeholder="e.g. login, created" value="{{ request('action') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                    value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                    value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('reports.audit-trail') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $log->user?->name ?? 'System' }}</div>
                            <div class="text-muted" style="font-size:0.75rem">{{ $log->user?->email }}</div>
                        </td>
                        <td><code class="small text-primary">{{ $log->action }}</code></td>
                        <td class="small text-muted" style="max-width:280px">
                            {{ Str::limit($log->description ?? '—', 80) }}
                        </td>
                        <td class="small text-muted font-monospace">{{ $log->ip_address ?? '—' }}</td>
                        <td class="small text-muted text-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
