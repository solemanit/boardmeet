{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card { border-radius: 10px; border: 1px solid rgba(0,0,0,.08); transition: box-shadow .15s; }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .meeting-item { border-bottom: 1px solid rgba(0,0,0,.06); transition: background .12s; }
    .meeting-item:last-child { border-bottom: none; }
    .meeting-item:hover { background: rgba(0,0,0,.02); }
    .date-block { width: 46px; min-width: 46px; text-align: center; background: #f8f9fa; border-radius: 8px; padding: 6px 4px; }
    .section-card { border-radius: 10px; border: 1px solid rgba(0,0,0,.08); overflow: hidden; }
    .section-head { padding: .85rem 1.1rem; border-bottom: 1px solid rgba(0,0,0,.07); display: flex; align-items: center; justify-content: space-between; background: #fff; }
    .qa-btn { display: flex; align-items: center; gap: .5rem; padding: .5rem .85rem; border: 1px solid rgba(0,0,0,.1); border-radius: 8px; font-size: 13px; font-weight: 500; color: #344054; background: #f9fafb; text-decoration: none; transition: all .12s; }
    .qa-btn:hover { background: #fff; border-color: #d0d5dd; color: #344054; text-decoration: none; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
    .circular-item { padding: .75rem 1.1rem; border-bottom: 1px solid rgba(0,0,0,.06); display: flex; align-items: flex-start; gap: .65rem; transition: background .12s; }
    .circular-item:last-child { border-bottom: none; }
    .circular-item:hover { background: #f8f9fa; }
    .type-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
    .vote-item { padding: .65rem 1rem; border-bottom: 1px solid rgba(0,0,0,.06); font-size: 12px; }
    .vote-item:last-child { border-bottom: none; }
    .badge-type { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 20px; white-space: nowrap; }
    .badge-board     { background: #EEEDFE; color: #3C3489; }
    .badge-agm       { background: #d1fae5; color: #065f46; }
    .badge-egm       { background: #fee2e2; color: #991b1b; }
    .badge-committee { background: #dbeafe; color: #1e40af; }
    .badge-executive { background: #fef3c7; color: #92400e; }
    .badge-other     { background: #f3f4f6; color: #374151; }
    .badge-scheduled { background: #dbeafe; color: #1e40af; }
    .badge-ongoing   { background: #fef3c7; color: #92400e; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-postponed { background: #f3f4f6; color: #374151; }
    .pending-header  { padding: .75rem 1rem; border-bottom: 1px solid #fcd9bd; background: #fff7ed; display: flex; align-items: center; gap: .4rem; }
</style>
@endpush

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Good {{ $greeting }}, {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0" style="font-size:13px;">{{ now()->format('l, d F Y') }} &middot; BoardMeet Overview</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @can('create', App\Models\Meeting::class)
        <a href="{{ route('meetings.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            New Meeting
        </a>
        @endcan
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
            Reports
        </a>
    </div>
</div>

{{-- ── Stats Row ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em;">Total Meetings</span>
                    <div class="stat-icon" style="background:#dbeafe;">
                        <svg width="18" height="18" fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.8rem; line-height:1.1;">{{ number_format($stats['total']) }}</div>
                <div class="d-flex gap-1 flex-wrap">
                    <span class="badge-type badge-scheduled">{{ $stats['scheduled'] }} scheduled</span>
                    <span class="badge-type badge-ongoing">{{ $stats['ongoing'] }} ongoing</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em;">Active Members</span>
                    <div class="stat-icon" style="background:#d1fae5;">
                        <svg width="18" height="18" fill="none" stroke="#065f46" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.8rem; line-height:1.1;">{{ number_format($stats['active_members']) }}</div>
                <div class="text-muted" style="font-size:12px;">{{ $stats['inactive_members'] }} inactive &middot; {{ $stats['total_members'] }} total</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em;">Circulars</span>
                    <div class="stat-icon" style="background:#fef3c7;">
                        <svg width="18" height="18" fill="none" stroke="#92400e" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.8rem; line-height:1.1;">{{ number_format($stats['published_circulars']) }}</div>
                <div class="text-muted" style="font-size:12px;">{{ $stats['draft_circulars'] }} draft &middot; {{ $stats['total_circulars'] }} total</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-medium" style="font-size:11px; text-transform:uppercase; letter-spacing:.05em;">Completed</span>
                    <div class="stat-icon" style="background:#d1fae5;">
                        <svg width="18" height="18" fill="none" stroke="#065f46" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    </div>
                </div>
                <div class="fw-bold mb-1" style="font-size:1.8rem; line-height:1.1;">{{ number_format($stats['completed']) }}</div>
                <div class="text-muted" style="font-size:12px;">{{ $stats['cancelled'] }} cancelled this year</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Main Row ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">

    {{-- Upcoming Meetings --}}
    <div class="col-12 col-xl-8">
        <div class="section-card bg-white h-100">
            <div class="section-head">
                <div>
                    <span class="fw-semibold" style="font-size:14px;">Upcoming Meetings</span>
                    <span class="text-muted ms-2" style="font-size:12px;">next 30 days</span>
                </div>
                <a href="{{ route('meetings.index') }}" class="text-primary text-decoration-none" style="font-size:12px;">View all</a>
            </div>
            @forelse($upcomingMeetings as $meeting)
            <a href="{{ route('meetings.show', $meeting) }}" class="d-block text-decoration-none text-reset">
                <div class="meeting-item d-flex align-items-center gap-3 px-3 py-2">
                    <div class="date-block">
                        <div class="text-muted fw-medium" style="font-size:10px; text-transform:uppercase; line-height:1;">{{ $meeting->date->format('M') }}</div>
                        <div class="fw-bold" style="font-size:18px; line-height:1.2; color:#111827;">{{ $meeting->date->format('d') }}</div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-medium text-truncate" style="font-size:13px; color:#111827;">{{ $meeting->title }}</div>
                        <div class="text-muted d-flex align-items-center gap-1 flex-wrap mt-1" style="font-size:11px;">
                            <span>{{ $meeting->meeting_number }}</span>
                            <span>&middot;</span>
                            <span>{{ $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('g:i A') : 'TBD' }}</span>
                            @if($meeting->venue)
                            <span>&middot;</span>
                            <span class="text-truncate" style="max-width:130px;">{{ $meeting->venue }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-1 align-items-end">
                        <span class="badge-type badge-{{ $meeting->type }}">{{ ucfirst($meeting->type) }}</span>
                        <span class="badge-type badge-{{ $meeting->status }}">{{ ucfirst($meeting->status) }}</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center text-muted py-5" style="font-size:13px;">
                <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="d-block mx-auto mb-2 opacity-25"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                No upcoming meetings in the next 30 days.
                @can('create', App\Models\Meeting::class)
                <br><a href="{{ route('meetings.create') }}" class="text-primary fw-medium">Schedule one now</a>
                @endcan
            </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-12 col-xl-4 d-flex flex-column gap-3">

        {{-- Quick Actions --}}
        <div class="section-card bg-white">
            <div class="section-head">
                <span class="fw-semibold" style="font-size:14px;">Quick Actions</span>
            </div>
            <div class="p-3 d-flex flex-column gap-2">
                @can('create', App\Models\Meeting::class)
                <a href="{{ route('meetings.create') }}" class="qa-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Schedule Meeting
                </a>
                @endcan
                @if(auth()->user()->hasAnyRole(['admin','md']))
                <a href="{{ route('circulars.create') }}" class="qa-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    Draft Circular
                </a>
                <a href="{{ route('members.create') }}" class="qa-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M12 14c-5 0-8 2-8 4v1h16v-1c0-2-3-4-8-4z"/></svg>
                    Add Member
                </a>
                @endif
                <a href="{{ route('reports.meetings') }}" class="qa-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
                    Meeting Reports
                </a>
                <a href="{{ route('reports.attendance') }}" class="qa-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16,11 18,13 22,9"/></svg>
                    Attendance Report
                </a>
            </div>
        </div>

        {{-- Pending Votes --}}
        @if($myPendingVotes->isNotEmpty())
        <div class="section-card bg-white" style="border-color:#fed7aa !important;">
            <div class="pending-header">
                <svg width="14" height="14" fill="none" stroke="#c2410c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span class="fw-semibold" style="font-size:12px; color:#c2410c;">Awaiting Your Vote ({{ $myPendingVotes->count() }})</span>
            </div>
            @foreach($myPendingVotes->take(4) as $memo)
            <div class="vote-item">
                <div class="fw-medium text-truncate" style="color:#111827;">{{ $memo->title }}</div>
                <div class="d-flex align-items-center justify-content-between mt-1 text-muted">
                    <span>{{ $memo->section->meeting->meeting_number }}</span>
                    <a href="{{ route('meetings.agenda-builder', $memo->section->meeting) }}" class="text-primary text-decoration-none fw-medium" style="font-size:11px;">Vote now</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

{{-- ── Second Row ────────────────────────────────────────────────────── --}}
<div class="row g-3">

    {{-- Charts --}}
    <div class="col-12 col-xl-8">
        <div class="section-card bg-white">
            <div class="section-head">
                <span class="fw-semibold" style="font-size:14px;">Meetings by Status &amp; Type — {{ now()->year }}</span>
            </div>
            <div class="row g-0 p-3">
                <div class="col-6 text-center">
                    <p class="text-muted mb-2" style="font-size:12px; font-weight:500;">By Status</p>
                    <div style="position:relative; height:180px;">
                        <canvas id="statusChart" role="img" aria-label="Meetings by status donut chart">Scheduled: {{ $stats['scheduled'] }}, Ongoing: {{ $stats['ongoing'] }}, Completed: {{ $stats['completed'] }}, Cancelled: {{ $stats['cancelled'] }}</canvas>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-2" style="font-size:11px; color:#6b7280;">
                        <span class="d-flex align-items-center gap-1"><span style="width:8px;height:8px;border-radius:2px;background:#3b82f6;display:inline-block;"></span>Scheduled {{ $stats['scheduled'] }}</span>
                        <span class="d-flex align-items-center gap-1"><span style="width:8px;height:8px;border-radius:2px;background:#f59e0b;display:inline-block;"></span>Ongoing {{ $stats['ongoing'] }}</span>
                        <span class="d-flex align-items-center gap-1"><span style="width:8px;height:8px;border-radius:2px;background:#10b981;display:inline-block;"></span>Completed {{ $stats['completed'] }}</span>
                        <span class="d-flex align-items-center gap-1"><span style="width:8px;height:8px;border-radius:2px;background:#ef4444;display:inline-block;"></span>Cancelled {{ $stats['cancelled'] }}</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <p class="text-muted mb-2" style="font-size:12px; font-weight:500;">By Type</p>
                    <div style="position:relative; height:180px;">
                        <canvas id="typeChart" role="img" aria-label="Meetings by type donut chart">{{ collect($meetingsByType)->map(fn($c,$t) => "$t: $c")->join(', ') }}</canvas>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-2" style="font-size:11px; color:#6b7280;">
                        @php $typeColors = ['board'=>'#7c3aed','agm'=>'#10b981','egm'=>'#ef4444','committee'=>'#3b82f6','executive'=>'#f59e0b','other'=>'#9ca3af']; @endphp
                        @foreach($meetingsByType as $type => $count)
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:8px;height:8px;border-radius:2px;background:{{ $typeColors[$type] ?? '#9ca3af' }};display:inline-block;"></span>
                            {{ ucfirst($type) }} {{ $count }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Circulars --}}
    <div class="col-12 col-xl-4">
        <div class="section-card bg-white h-100">
            <div class="section-head">
                <span class="fw-semibold" style="font-size:14px;">Recent Circulars</span>
                <a href="{{ route('circulars.index') }}" class="text-primary text-decoration-none" style="font-size:12px;">View all</a>
            </div>
            @forelse($recentCirculars as $circular)
            <a href="{{ route('circulars.show', $circular) }}" class="d-block text-decoration-none text-reset">
                <div class="circular-item">
                    @php $dotColors = ['notice'=>'#f59e0b','circular'=>'#3b82f6','memo'=>'#10b981']; @endphp
                    <div class="type-dot" style="background:{{ $dotColors[$circular->type] ?? '#9ca3af' }};"></div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-medium text-truncate" style="font-size:12px; color:#111827;">{{ $circular->title }}</div>
                        <div class="text-muted d-flex gap-1 mt-1" style="font-size:11px;">
                            <span>{{ $circular->reference_number }}</span>
                            <span>&middot;</span>
                            <span>{{ $circular->issued_date->format('d M Y') }}</span>
                        </div>
                    </div>
                    <span class="badge-type {{ $circular->isPublished() ? 'badge-completed' : 'badge-other' }}">
                        {{ $circular->isPublished() ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </a>
            @empty
            <div class="text-center text-muted py-4" style="font-size:13px;">No circulars yet.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Recent Activity ───────────────────────────────────────────────── --}}
@if($recentActivity->isNotEmpty())
<div class="section-card bg-white mt-3">
    <div class="section-head">
        <span class="fw-semibold" style="font-size:14px;">Recent Activity</span>
        <a href="{{ route('activity.index') }}" class="text-primary text-decoration-none" style="font-size:12px;">View all</a>
    </div>
    <div class="row g-0">
        @foreach($recentActivity->take(10) as $activity)
        <div class="col-12 col-md-6">
            <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom" style="font-size:13px;">
                @php $dotColor = ['created'=>'#3b82f6','updated'=>'#10b981','deleted'=>'#ef4444','completed'=>'#10b981','cancelled'=>'#f59e0b'][$activity->event] ?? '#9ca3af'; @endphp
                <span style="width:7px;height:7px;border-radius:50%;background:{{ $dotColor }};margin-top:5px;flex-shrink:0;display:inline-block;"></span>
                <div>
                    <span class="fw-medium">{{ $activity->causer?->name ?? 'System' }}</span>
                    <span class="text-muted"> {{ $activity->description }}</span>
                    <div class="text-muted mt-1" style="font-size:11px;">{{ $activity->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
(function () {
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Scheduled', 'Ongoing', 'Completed', 'Cancelled'],
            datasets: [{
                data: [{{ $stats['scheduled'] }}, {{ $stats['ongoing'] }}, {{ $stats['completed'] }}, {{ $stats['cancelled'] }}],
                backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } } }
        }
    });

    const typeColors = { board:'#7c3aed', agm:'#10b981', egm:'#ef4444', committee:'#3b82f6', executive:'#f59e0b', other:'#9ca3af' };
    const typeData   = @json($meetingsByType);
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(typeData).map(t => t.charAt(0).toUpperCase() + t.slice(1)),
            datasets: [{
                data: Object.values(typeData),
                backgroundColor: Object.keys(typeData).map(t => typeColors[t] ?? '#9ca3af'),
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } } }
        }
    });
})();
</script>
@endpush
