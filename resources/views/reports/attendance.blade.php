@extends('layouts.app')
@section('title', 'Attendance Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Attendance Report</h4>
        <p class="text-muted mb-0">Per-meeting and per-user attendance breakdown</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">Export PDF</a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.attendance') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['scheduled','ongoing','completed','cancelled','postponed'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('reports.attendance') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Per-Meeting Table --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0">Per-Meeting Breakdown</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Meeting</th>
                        <th>Date</th>
                        <th class="text-center">Invited</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $i => $meeting)
                    @php
                        $rate     = $meeting->attendees_count > 0
                            ? round(($meeting->present_count / $meeting->attendees_count) * 100)
                            : 0;
                        $barColor = $rate >= 75 ? 'bg-success' : ($rate >= 50 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $meeting->meeting_number }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ Str::limit($meeting->title, 45) }}</div>
                        </td>
                        <td class="small text-nowrap">{{ $meeting->date?->format('d M Y') ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $meeting->attendees_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-label-success">{{ $meeting->present_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-label-danger">{{ $meeting->absent_count }}</span>
                        </td>
                        <td style="min-width:130px">
                            @if($meeting->attendees_count > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar {{ $barColor }}" style="width:{{ $rate }}%"></div>
                                    </div>
                                    <span class="small fw-semibold">{{ $rate }}%</span>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $meeting->getStatusBadgeClass() }}">{{ ucfirst($meeting->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">No meetings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Per-User Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0">Per-User Attendance Summary</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Invited</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th>Attendance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userAttendance as $i => $u)
                    @php
                        $barColor = $u->attendance_rate >= 75 ? 'bg-success' : ($u->attendance_rate >= 50 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td class="fw-semibold small">{{ $u->user?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $u->user?->email ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $u->total_invited }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-label-success">{{ $u->total_present }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-label-danger">{{ $u->total_absent }}</span>
                        </td>
                        <td style="min-width:140px">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px">
                                    <div class="progress-bar {{ $barColor }}" style="width:{{ $u->attendance_rate }}%"></div>
                                </div>
                                <span class="small fw-semibold">{{ $u->attendance_rate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No attendance data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
