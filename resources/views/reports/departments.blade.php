@extends('layouts.app')
@section('title', 'Department-wise Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Department-wise Report</h4>
        <p class="text-muted mb-0">Member distribution and headcount by department</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">Export PDF</a>
    </div>
</div>

{{-- Department Summary Cards --}}
<div class="row g-3 mb-4">
    @forelse($departmentStats as $dept)
    <div class="col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3 text-truncate" title="{{ $dept->department ?: 'Unassigned' }}">
                    {{ $dept->department ?: 'Unassigned' }}
                </h6>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <div class="fs-2 fw-bold text-primary">{{ $dept->total }}</div>
                        <div class="text-muted small">Total Members</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-success fw-semibold">{{ $dept->active_count }} Active</div>
                        <div class="small text-danger fw-semibold">{{ $dept->inactive_count }} Inactive</div>
                    </div>
                </div>
                @php $rate = $dept->total > 0 ? round(($dept->active_count / $dept->total) * 100) : 0; @endphp
                <div class="progress mt-3" style="height:5px">
                    <div class="progress-bar bg-success" style="width:{{ $rate }}%"></div>
                </div>
                <div class="text-muted mt-1" style="font-size:.7rem">{{ $rate }}% active</div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">No department data found.</div>
    @endforelse
</div>

{{-- Members by Department --}}
@forelse($byDepartment as $deptName => $members)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">{{ $deptName ?: 'Unassigned' }}</h6>
        <span class="badge badge-label-primary">{{ $members->count() }} members</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $i => $member)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $member->name }}</div>
                        </td>
                        <td class="text-muted small">{{ $member->designation ?? '—' }}</td>
                        <td class="text-muted small">{{ $member->email ?? '—' }}</td>
                        <td class="text-muted small">{{ $member->phone ?? '—' }}</td>
                        <td class="text-muted small">{{ $member->joining_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $member->status === 'active' ? 'badge-label-success' : 'badge-label-secondary' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">No members found.</div>
</div>
@endforelse
@endsection
