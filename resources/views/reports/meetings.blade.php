@extends('layouts.app')
@section('title', 'Meeting Report')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Meeting Report</h4>
        <p class="text-muted mb-0">Overview of all meetings</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
           class="btn btn-sm btn-danger">
            Export PDF
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.meetings') }}" class="row g-3 align-items-end">
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
                <label class="form-label small fw-semibold">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(['board','agm','egm','committee','executive'] as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ strtoupper($t) }}</option>
                    @endforeach
                </select>
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
                <a href="{{ route('reports.meetings') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    @foreach([['label'=>'Total','value'=>$summary['total'],'color'=>'primary'],['label'=>'Scheduled','value'=>$summary['scheduled'],'color'=>'info'],['label'=>'Completed','value'=>$summary['completed'],'color'=>'success'],['label'=>'Cancelled','value'=>$summary['cancelled'],'color'=>'danger']] as $item)
    <div class="col-6 col-md-3">
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
                        <th>Meeting No.</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Chairperson</th>
                        <th>Attendees</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $i => $meeting)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td><code class="small">{{ $meeting->meeting_number }}</code></td>
                        <td>
                            <a href="{{ route('meetings.show', $meeting) }}" class="text-body fw-semibold text-decoration-none">
                                {{ Str::limit($meeting->title, 45) }}
                            </a>
                        </td>
                        <td><span class="badge {{ $meeting->getTypeBadgeClass() }}">{{ strtoupper($meeting->type) }}</span></td>
                        <td class="small">{{ $meeting->date?->format('d M Y') ?? '—' }}</td>
                        <td class="small">{{ $meeting->chairperson?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $meeting->attendees_count }}</span>
                        </td>
                        <td><span class="badge {{ $meeting->getStatusBadgeClass() }}">{{ ucfirst($meeting->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">No meetings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
