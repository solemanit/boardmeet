@extends('layouts.app')
@section('title', 'Minutes & Extract')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Minutes & Extract</h4>
        <p class="text-muted mb-0">Formal meeting minutes with agenda items and decisions</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        @if($meeting)
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">
                Export PDF
            </a>
        @endif
    </div>
</div>

{{-- Meeting Selector --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.minutes') }}" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label class="form-label small fw-semibold">Select Completed Meeting</label>
                <select name="meeting_id" class="form-select form-select-sm">
                    <option value="">— Choose a meeting —</option>
                    @foreach($meetings as $m)
                        <option value="{{ $m->id }}" @selected(request('meeting_id') == $m->id)>
                            {{ $m->meeting_number }} — {{ $m->title }} ({{ $m->date?->format('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Load Minutes</button>
            </div>
        </form>
    </div>
</div>

@if($meeting)
{{-- Meeting Header --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small fw-semibold" style="width:130px">Meeting No.</td>
                        <td class="small"><code>{{ $meeting->meeting_number }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Title</td>
                        <td class="small fw-semibold">{{ $meeting->title }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Date</td>
                        <td class="small">{{ $meeting->date?->format('l, d F Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Time</td>
                        <td class="small">{{ $meeting->start_time ?? '—' }} – {{ $meeting->end_time ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small fw-semibold" style="width:130px">Venue</td>
                        <td class="small">{{ $meeting->venue ?? '—' }}{{ $meeting->venue_type === 'online' ? ' (Online)' : '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Chairperson</td>
                        <td class="small">{{ $meeting->chairperson?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Secretary</td>
                        <td class="small">{{ $meeting->secretary?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Status</td>
                        <td><span class="badge {{ $meeting->getStatusBadgeClass() }}">{{ ucfirst($meeting->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Attendees --}}
@php
    $present = $meeting->attendees->where('status', 'present');
    $absent  = $meeting->attendees->where('status', 'absent');
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">Attendees</h6>
        <div class="d-flex gap-2">
            <span class="badge badge-label-success">{{ $present->count() }} Present</span>
            <span class="badge badge-label-danger">{{ $absent->count() }} Absent</span>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($present as $att)
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                    ✓ {{ $att->user?->name ?? '—' }}
                </span>
            @endforeach
            @foreach($absent as $att)
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                    ✗ {{ $att->user?->name ?? '—' }}
                </span>
            @endforeach
            @if($present->isEmpty() && $absent->isEmpty())
                <span class="text-muted small">No attendee records.</span>
            @endif
        </div>
    </div>
</div>

{{-- Agenda Items / Minutes --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0">Agenda Items & Minutes</h6>
    </div>
    <div class="card-body p-0">
        @forelse($agendaItems as $item)
        <div class="border-bottom p-4">
            <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:32px;height:32px;font-size:.85rem">
                    {{ $item->order ?? $loop->iteration }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h6 class="fw-bold mb-0">{{ $item->title }}</h6>
                        @if($item->duration_minutes)
                            <span class="badge bg-light text-muted border small">{{ $item->duration_minutes }} min</span>
                        @endif
                    </div>

                    @if($item->description)
                        <p class="text-muted small mb-2">{{ $item->description }}</p>
                    @endif

                    @if($item->notes)
                        <div class="rounded bg-light p-3 mb-2">
                            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size:.7rem;letter-spacing:.5px">Discussion / Minutes</div>
                            <p class="small mb-0">{{ $item->notes }}</p>
                        </div>
                    @endif

                    @if($item->action_required)
                        <div class="rounded border-start border-warning border-3 bg-warning bg-opacity-10 p-3">
                            <div class="text-warning fw-semibold small mb-1">⚡ Action Required</div>
                            <p class="small mb-1">{{ $item->action_required }}</p>
                            <div class="d-flex gap-3 text-muted" style="font-size:.75rem">
                                @if($item->action_owner)
                                    <span>Owner: <strong>{{ $item->action_owner }}</strong></span>
                                @endif
                                @if($item->action_deadline)
                                    <span>Due: <strong>{{ \Carbon\Carbon::parse($item->action_deadline)->format('d M Y') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">No agenda items recorded for this meeting.</div>
        @endforelse
    </div>
</div>

@else
{{-- Empty state --}}
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <svg class="text-muted mb-3" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h6 class="text-muted">Select a completed meeting above to view its minutes.</h6>
    </div>
</div>
@endif
@endsection
