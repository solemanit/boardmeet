@extends('layouts.app')
@section('title', 'Member Activity Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Member Activity Report</h4>
        <p class="text-muted mb-0">Member participation & meeting attendance summary</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">
            Export PDF
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.member-activity') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Department</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active"   @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('reports.member-activity') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary badges --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-primary">{{ $members->count() }}</div>
            <div class="text-muted small">Total Members</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ $members->where('status', 'active')->count() }}</div>
            <div class="text-muted small">Active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-danger">{{ $members->where('status', 'inactive')->count() }}</div>
            <div class="text-muted small">Inactive</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-info">{{ $departments->count() }}</div>
            <div class="text-muted small">Departments</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th class="text-center">Meetings</th>
                        <th class="text-center">Attended</th>
                        <th>Attendance Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $i => $member)
                    @php
                        $att      = $attendanceSummary[$member->email] ?? null;
                        $total    = $att?->total_meetings ?? 0;
                        $attended = $att?->attended ?? 0;
                        $rate     = $total > 0 ? round(($attended / $total) * 100) : 0;
                        $barColor = $rate >= 75 ? 'bg-success' : ($rate >= 50 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $member->name }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $member->email ?? '—' }}</div>
                        </td>
                        <td class="small text-muted">{{ $member->department ?? '—' }}</td>
                        <td class="small text-muted">{{ $member->designation ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $total }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $attended }}</span>
                        </td>
                        <td style="min-width: 140px">
                            @if($total > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar {{ $barColor }}" style="width:{{ $rate }}%"></div>
                                    </div>
                                    <span class="small fw-semibold text-nowrap">{{ $rate }}%</span>
                                </div>
                            @else
                                <span class="text-muted small">No data</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $member->status === 'active' ? 'badge-label-success' : 'badge-label-secondary' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
