@extends('layouts.app')
@section('page-title', 'Vote Report — ' . $meeting->meeting_number)
@section('title', 'Vote Report')

@push('styles')
<style>
.vr-summary-card {
    background:#fff; border:1.5px solid #e2e8f0; border-radius:10px;
    padding:18px 22px; display:flex; align-items:center; gap:14px;
}
.vr-summary-icon {
    width:44px; height:44px; border-radius:10px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.vr-stat-num  { font-size:22px; font-weight:800; color:#1e293b; line-height:1; }
.vr-stat-lbl  { font-size:11px; color:#64748b; margin-top:2px; }

.memo-report-card {
    background:#fff; border:1.5px solid #e2e8f0; border-radius:10px;
    overflow:hidden; margin-bottom:16px;
}
.memo-rh { background:#f8fafc; padding:14px 18px; border-bottom:1.5px solid #e2e8f0; }
.memo-rh-title { font-weight:700; font-size:14px; color:#1e293b; }

.result-bar { display:flex; height:8px; border-radius:4px; overflow:hidden; background:#f1f5f9; }
.rb-a { background:#16a34a; }
.rb-r { background:#dc2626; }
.rb-x { background:#94a3b8; }

.oc-passed   { background:#dcfce7; color:#166534; }
.oc-rejected { background:#fee2e2; color:#991b1b; }
.oc-tied     { background:#fef9c3; color:#854d0e; }
.oc-pending  { background:#f1f5f9; color:#64748b; }

.voter-table th { font-size:11px; text-transform:uppercase; font-weight:700; color:#94a3b8; letter-spacing:.5px; }
.voter-table td { font-size:12px; vertical-align:middle; }
.vote-chip {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10px; font-weight:700; padding:3px 10px; border-radius:20px;
}
.vc-approve { background:#dcfce7; color:#166534; }
.vc-reject  { background:#fee2e2; color:#991b1b; }
.vc-abstain { background:#f1f5f9; color:#475569; }
</style>
@endpush

@section('content')

<div class="page-header pb-6 d-flex justify-content-between align-items-center gap-4 flex-wrap">
    <div>
        <h2 class="fw-semibold fs-7 mb-1">Vote Report</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->meeting_number }}</a></li>
                <li class="breadcrumb-item active">Vote Report</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('meetings.vote-report.pdf', $meeting) }}" target="_blank"
           class="btn btn-primary d-flex align-items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 15V3M12 15L8 11M12 15L16 11M3 17V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('meetings.agenda-builder', $meeting) }}" class="btn btn-outline-secondary">← Agenda Builder</a>
    </div>
</div>

{{-- Meeting summary --}}
<div class="card shadow-custom rounded-custom mb-5">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <small class="text-custom-paragraph d-block">Meeting</small>
                <strong>{{ $meeting->meeting_number }}</strong> · {{ Str::limit($meeting->title, 40) }}
            </div>
            <div class="col-md-2">
                <small class="text-custom-paragraph d-block">Date</small>
                <strong>{{ $meeting->date->format('d M Y') }}</strong>
            </div>
            <div class="col-md-2">
                <small class="text-custom-paragraph d-block">Total Votes Cast</small>
                <strong class="text-primary">{{ $reportData['total_votes_cast'] }}</strong>
            </div>
            @if($reportData['link'])
            <div class="col-md-4">
                <small class="text-custom-paragraph d-block">Vote Link Usage</small>
                <strong>{{ $reportData['link']->use_count }}</strong>
                {{ $reportData['link']->max_uses ? '/ ' . $reportData['link']->max_uses : '' }} submissions
                @if($reportData['link']->isExpired()) <span class="badge bg-secondary ms-1">Expired</span> @endif
                @if(!$reportData['link']->is_active) <span class="badge bg-danger ms-1">Revoked</span> @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="vr-summary-card">
            <div class="vr-summary-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 5.55228 9.44772 6 10 6H14C14.5523 6 15 5.55228 15 5M9 5C9 4.44772 9.44772 4 10 4H14C14.5523 4 15 4.44772 15 5" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="vr-stat-num">{{ $reportData['total_votable'] }}</div>
                <div class="vr-stat-lbl">Voting Items</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="vr-summary-card">
            <div class="vr-summary-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="vr-stat-num text-success">{{ $reportData['total_passed'] }}</div>
                <div class="vr-stat-lbl">Passed</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="vr-summary-card">
            <div class="vr-summary-icon" style="background:#fef2f2;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="vr-stat-num text-danger">{{ $reportData['total_rejected'] }}</div>
                <div class="vr-stat-lbl">Rejected</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="vr-summary-card">
            <div class="vr-summary-icon" style="background:#f8fafc;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#64748b" stroke-width="1.5"/><path d="M12 8V12M12 16H12.01" stroke="#64748b" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="vr-stat-num">{{ $reportData['total_votes_cast'] }}</div>
                <div class="vr-stat-lbl">Total Votes</div>
            </div>
        </div>
    </div>
</div>

{{-- Per section / memo results --}}
@forelse($reportData['sections'] as $sData)
<div class="mb-5">
    <h5 class="fw-bold mb-3" style="color:#1e293b;">
        {{ $loop->iteration }}. {{ $sData['section']->title }}
    </h5>

    @foreach($sData['memos'] as $mData)
    @php $m = $mData['memo']; $t = $mData['tally']; @endphp
    <div class="memo-report-card">
        <div class="memo-rh d-flex align-items-start gap-3 flex-wrap">
            <div class="flex-grow-1">
                <div class="memo-rh-title">{{ $m->title }}</div>
                @if($m->description)
                    <div style="font-size:12px;color:#64748b;margin-top:3px;">{{ Str::limit($m->description,120) }}</div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <span class="badge" style="font-size:11px;font-weight:700;padding:5px 12px;"
                      class="oc-{{ $mData['outcome'] }}"
                      style="background:{{ match($mData['outcome']) { 'passed'=>'#dcfce7','rejected'=>'#fee2e2','tied'=>'#fef9c3',default=>'#f1f5f9'} }};
                             color:{{ match($mData['outcome']) { 'passed'=>'#166534','rejected'=>'#991b1b','tied'=>'#854d0e',default=>'#64748b'} }};">
                    {{ ucfirst($mData['outcome']) }}
                </span>
                @if($m->vote_required === 'required')
                    <span class="badge" style="background:#fffbeb;color:#92400e;font-size:10px;">Required</span>
                @endif
            </div>
        </div>
        <div class="p-4">
            {{-- Vote bar --}}
            @if($t['total'] > 0)
            <div class="result-bar mb-2" style="height:10px;border-radius:5px;">
                <div class="rb-a" style="width:{{ $mData['pct_approve'] }}%"></div>
                <div class="rb-r" style="width:{{ $mData['pct_reject'] }}%"></div>
                <div class="rb-x" style="width:{{ $mData['pct_abstain'] }}%"></div>
            </div>
            <div class="d-flex gap-4 mb-4" style="font-size:12px;">
                <span><span style="color:#16a34a;font-weight:700;">✓ {{ $t['approve'] }}</span> Approve ({{ $mData['pct_approve'] }}%)</span>
                <span><span style="color:#dc2626;font-weight:700;">✕ {{ $t['reject'] }}</span> Reject ({{ $mData['pct_reject'] }}%)</span>
                <span><span style="color:#64748b;font-weight:700;">— {{ $t['abstain'] }}</span> Abstain ({{ $mData['pct_abstain'] }}%)</span>
                <span class="ms-auto text-custom-paragraph">{{ $t['total'] }} total vote{{ $t['total'] !== 1 ? 's' : '' }}</span>
            </div>
            @else
            <p class="text-custom-paragraph small fst-italic mb-4">No votes recorded yet.</p>
            @endif

            {{-- Voter breakdown --}}
            @if($mData['all_voters']->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm mb-0 voter-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Vote</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mData['all_voters'] as $vi => $voter)
                        <tr>
                            <td style="color:#94a3b8;">{{ $vi + 1 }}</td>
                            <td class="fw-medium">{{ $voter['name'] }}</td>
                            <td>{{ $voter['email'] }}</td>
                            <td>
                                <span class="badge {{ $voter['type'] === 'user' ? 'bg-info' : 'bg-secondary' }} bg-opacity-25 text-dark" style="font-size:10px;">
                                    {{ ucfirst($voter['type']) }}
                                </span>
                            </td>
                            <td>
                                <span class="vote-chip vc-{{ $voter['vote'] }}">
                                    {{ match($voter['vote']) { 'approve'=>'✓ Approve', 'reject'=>'✕ Reject', default=>'— Abstain' } }}
                                </span>
                            </td>
                            <td style="color:#94a3b8;">{{ $voter['voted_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@empty
<div class="card shadow-custom rounded-custom">
    <div class="card-body text-center py-5 text-custom-paragraph">
        <p>No voting data available for this meeting.</p>
        <a href="{{ route('meetings.agenda-builder', $meeting) }}" class="btn btn-primary btn-sm">Go to Agenda Builder</a>
    </div>
</div>
@endforelse

@endsection
