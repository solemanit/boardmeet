{{-- views/meetings/minutes.blade.php --}}
@extends('layouts.app')
@section('page-title', 'Minutes — ' . $meeting->meeting_number)
@section('title', 'Meeting Minutes')

@push('styles')
<style>
/* ── Section block ─────────────────────────────────────────────── */
.mn-section {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.mn-section-header {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px;
    background: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
}
.mn-section-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 24px; height: 24px; min-width: 24px; border-radius: 50%;
    background: var(--bs-primary,#0d6efd); color: #fff;
    font-size: 10px; font-weight: 700;
}
.mn-section-title { font-weight: 700; font-size: 14px; color: #1e293b; }
.mn-section-pill {
    margin-left: auto; font-size: 10px; font-weight: 700;
    background: #e2e8f0; color: #64748b;
    border-radius: 20px; padding: 2px 10px;
}

/* ── Memo card ─────────────────────────────────────────────────── */
.mn-memo {
    border-bottom: 1px solid #f1f5f9;
    background: #fff;
}
.mn-memo:last-child { border-bottom: none; }

.mn-memo-top {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 18px 10px;
}
.mn-memo-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px; min-width: 22px; border-radius: 50%;
    background: #e2e8f0; color: #475569;
    font-size: 10px; font-weight: 700; flex-shrink: 0; margin-top: 2px;
}
.mn-memo-title { font-weight: 600; font-size: 13px; color: #1e293b; margin: 0; }
.mn-memo-desc  { font-size: 12px; color: #64748b; margin: 3px 0 0; white-space: pre-wrap; line-height: 1.5; }
.mn-memo-meta  { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; margin-top: 5px; }

/* Vote badge */
.v-badge {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 700; border-radius: 20px; padding: 2px 8px;
    text-transform: uppercase; letter-spacing: .3px; border: 1px solid;
}
.v-badge.required { background:#fffbeb; color:#92400e; border-color:#fde68a; }
.v-badge.optional { background:#f0f9ff; color:#0369a1; border-color:#bae6fd; }

/* Vote result bar (compact) */
.vote-bar-row {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 18px 6px 52px; font-size: 11px;
    background: #f8fafc; border-top: 1px solid #f1f5f9;
}
.vbar { flex: 1; height: 5px; border-radius: 3px; background: #e2e8f0; overflow: hidden; display: flex; }
.vb-a { background: #16a34a; height: 100%; }
.vb-r { background: #dc2626; height: 100%; }
.vb-x { background: #94a3b8; height: 100%; }
.vote-tally { color: #64748b; white-space: nowrap; }
.oc-badge { font-size: 10px; font-weight: 700; border-radius: 20px; padding: 1px 7px; text-transform: uppercase; }
.oc-passed   { background: #dcfce7; color: #166534; }
.oc-rejected { background: #fee2e2; color: #991b1b; }
.oc-tied     { background: #fef9c3; color: #854d0e; }
.oc-pending  { background: #f1f5f9; color: #64748b; }

/* Files row */
.mn-files-row {
    display: flex; flex-wrap: wrap; gap: 5px;
    padding: 6px 18px 8px 52px;
}
.mn-file-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 6px;
    padding: 3px 8px; font-size: 11px; color: #374151; font-weight: 500;
    text-decoration: none; max-width: 220px;
}
.mn-file-chip:hover { border-color: var(--bs-primary,#0d6efd); color: var(--bs-primary,#0d6efd); }
.mn-file-chip-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Notes textarea */
.mn-note-area {
    padding: 10px 18px 14px 52px;
}
.mn-note-area textarea {
    width: 100%; resize: vertical; min-height: 90px;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 10px 12px; font-size: 13px; color: #1e293b; line-height: 1.6;
    transition: border-color .15s;
    background: #fff;
}
.mn-note-area textarea:focus { outline: none; border-color: var(--bs-primary,#0d6efd); }
.mn-note-area textarea.has-content { background: #f0fdf4; border-color: #86efac; }
.mn-note-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.mn-note-label .saved-pill {
    font-size: 10px; font-weight: 700; padding: 1px 8px; border-radius: 20px;
    background: #dcfce7; color: #166534; text-transform: none; letter-spacing: 0;
}

/* Autosave indicator */
.autosave-indicator {
    display: none; font-size: 11px; color: #16a34a;
    align-items: center; gap: 4px;
}
.autosave-indicator.show { display: flex; }

/* Attendance sidebar */
.att-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; border-bottom: 1px solid #f1f5f9;
}
.att-row:last-child { border-bottom: none; }
.att-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--bs-primary,#0d6efd); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}

/* Progress bar */
.minutes-progress {
    background: #f1f5f9; border-radius: 6px; height: 8px; overflow: hidden; margin-top: 8px;
}
.minutes-progress-bar {
    height: 100%; background: linear-gradient(90deg, #16a34a, #22c55e);
    border-radius: 6px; transition: width .4s;
}
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-header pb-6 d-flex justify-content-between align-items-center gap-4 flex-wrap">
    <div>
        <h2 class="fw-semibold fs-7 mb-1">Meeting Minutes</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->meeting_number }}</a></li>
                <li class="breadcrumb-item active">Minutes</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('meetings.agenda-builder', $meeting) }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Agenda Builder
        </a>
        <a href="{{ route('meetings.export.minutes', $meeting) }}" target="_blank"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 15V3M12 15L8 11M12 15L16 11M3 17V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V17"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary">View Meeting</a>
    </div>
</div>

@include('layouts.partials.alerts')

{{-- Meeting summary bar --}}
<div class="card shadow-custom rounded-custom mb-5">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-sm-6 col-md-2">
                <small class="text-custom-paragraph d-block mb-1">Meeting</small>
                <code class="fw-semibold">{{ $meeting->meeting_number }}</code>
            </div>
            <div class="col-sm-6 col-md-2">
                <small class="text-custom-paragraph d-block mb-1">Date</small>
                <span class="fw-medium">{{ $meeting->date->format('d M Y') }}</span>
            </div>
            <div class="col-sm-6 col-md-2">
                <small class="text-custom-paragraph d-block mb-1">Time</small>
                <span class="fw-medium">{{ $meeting->start_time }}{{ $meeting->end_time ? ' – '.$meeting->end_time : '' }}</span>
            </div>
            <div class="col-sm-6 col-md-2">
                <small class="text-custom-paragraph d-block mb-1">Chairperson</small>
                <span class="fw-medium">{{ $meeting->chairperson?->name ?? '—' }}</span>
            </div>
            <div class="col-sm-6 col-md-2">
                <small class="text-custom-paragraph d-block mb-1">Secretary</small>
                <span class="fw-medium">{{ $meeting->secretary?->name ?? '—' }}</span>
            </div>
            <div class="col-sm-6 col-md-2">
                {{-- Minutes completion progress --}}
                @php
                    $allMemos   = $meeting->agendaSections->flatMap->memos;
                    $totalMemos = $allMemos->count();
                    $notedMemos = $allMemos->whereNotNull('minutes_note')->where('minutes_note', '!=', '')->count();
                    $pct        = $totalMemos > 0 ? round($notedMemos / $totalMemos * 100) : 0;
                @endphp
                <small class="text-custom-paragraph d-block mb-1">Notes Progress</small>
                <span class="fw-semibold text-primary">{{ $notedMemos }}/{{ $totalMemos }}</span>
                <span class="text-custom-paragraph small"> memos</span>
                <div class="minutes-progress">
                    <div class="minutes-progress-bar" style="width:{{ $pct }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('meetings.minutes.save', $meeting) }}" method="POST" id="minutesForm">
@csrf

<div class="row g-5">

    {{-- ════ Minutes Column ═══════════════════════════════════════════ --}}
    <div class="col-lg-8">

        @forelse($meeting->agendaSections as $si => $section)

        {{-- ── Section block ─────────────────────────────────────────── --}}
        <div class="mn-section">
            <div class="mn-section-header">
                <span class="mn-section-num">{{ $si + 1 }}</span>
                <span class="mn-section-title">{{ $section->title }}</span>
                <span class="mn-section-pill">{{ $section->memos->count() }} memo{{ $section->memos->count() !== 1 ? 's' : '' }}</span>
            </div>

            @forelse($section->memos as $mi => $memo)
            @php
                $tally   = $memo->voteTally();
                $outcome = $memo->voteOutcome();
                $total   = $tally['total'] ?: 1;
                $myVote  = $memo->myVote ?? null;
            @endphp

            {{-- ── Memo ──────────────────────────────────────────────── --}}
            <div class="mn-memo">

                {{-- Memo header --}}
                <div class="mn-memo-top">
                    <span class="mn-memo-num">{{ $mi + 1 }}</span>
                    <div class="flex-grow-1">
                        <p class="mn-memo-title">{{ $memo->title }}</p>
                        @if($memo->description)
                            <p class="mn-memo-desc">{{ $memo->description }}</p>
                        @endif
                        <div class="mn-memo-meta">
                            @if($memo->vote_required !== 'none')
                                <span class="v-badge {{ $memo->vote_required }}">
                                    @if($memo->vote_required === 'required')
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    @endif
                                    Vote: {{ ucfirst($memo->vote_required) }}
                                </span>
                            @endif
                            @if($memo->documents->count() > 0)
                                <span style="font-size:10px;color:#64748b;">
                                    📎 {{ $memo->documents->count() }} file{{ $memo->documents->count() !== 1 ? 's' : '' }}
                                </span>
                            @endif
                            @if($memo->minutes_note)
                                <span style="font-size:10px;font-weight:700;background:#dcfce7;color:#166534;padding:2px 8px;border-radius:20px;">
                                    ✓ Note saved
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Vote results bar (if voting enabled) --}}
                @if($memo->vote_required !== 'none')
                <div class="vote-bar-row">
                    <div class="vbar">
                        <div class="vb-a" style="width:{{ round($tally['approve']/$total*100) }}%"></div>
                        <div class="vb-r" style="width:{{ round($tally['reject']/$total*100) }}%"></div>
                        <div class="vb-x" style="width:{{ round($tally['abstain']/$total*100) }}%"></div>
                    </div>
                    <span class="vote-tally">
                        {{ $tally['approve'] }}✓&nbsp; {{ $tally['reject'] }}✕&nbsp; {{ $tally['abstain'] }}—
                        &nbsp;·&nbsp; {{ $tally['total'] }} vote{{ $tally['total'] !== 1 ? 's' : '' }}
                    </span>
                    <span class="oc-badge oc-{{ $outcome }}">{{ ucfirst($outcome) }}</span>

                    {{-- My vote indicator --}}
                    @if($myVote)
                        <span style="font-size:10px;color:#64748b;">
                            (You: <strong>{{ ucfirst($myVote->vote) }}</strong>)
                        </span>
                    @endif
                </div>
                @endif

                {{-- Attached files --}}
                @if($memo->documents->isNotEmpty())
                <div class="mn-files-row">
                    @foreach($memo->documents as $doc)
                    <a href="{{ $doc->download_url }}" target="_blank" class="mn-file-chip" title="{{ $doc->original_name }}">
                        <span>{{ $doc->icon_emoji }}</span>
                        <span class="mn-file-chip-name">{{ $doc->original_name }}</span>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Minutes note textarea --}}
                <div class="mn-note-area">
                    <div class="mn-note-label">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Minutes / Discussion Notes
                        @if($memo->minutes_note)
                            <span class="saved-pill">Saved</span>
                        @endif
                        <span class="autosave-indicator ms-auto" id="saved-{{ $memo->id }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Saved
                        </span>
                    </div>
                    <textarea
                        name="memos[{{ $memo->id }}][note]"
                        id="note-{{ $memo->id }}"
                        class="mn-note-textarea {{ $memo->minutes_note ? 'has-content' : '' }}"
                        data-memo-id="{{ $memo->id }}"
                        placeholder="Record discussions, decisions, resolutions, and action items for this memo…">{{ old("memos.{$memo->id}.note", $memo->minutes_note) }}</textarea>
                </div>

            </div>
            {{-- END MEMO --}}

            @empty
            <div class="px-4 py-4 text-center text-custom-paragraph small fst-italic">
                No memos in this section.
                <a href="{{ route('meetings.agenda-builder', $meeting) }}">Add memos →</a>
            </div>
            @endforelse

        </div>
        {{-- END SECTION --}}

        @empty
        <div class="card shadow-custom rounded-custom">
            <div class="card-body text-center py-5 text-custom-paragraph">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mb-3 opacity-40 d-block mx-auto">
                    <path d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="fw-medium mb-2">No agenda sections defined yet.</p>
                <a href="{{ route('meetings.agenda-builder', $meeting) }}" class="btn btn-primary btn-sm">
                    Go to Agenda Builder
                </a>
            </div>
        </div>
        @endforelse

    </div>
    {{-- end minutes column --}}


    {{-- ════ Sidebar ═══════════════════════════════════════════════════ --}}
    <div class="col-lg-4">

        {{-- Attendance Card --}}
        <div class="pure-card rounded-custom card-bg shadow-custom mb-4">
            <div class="pure-card-header d-flex align-items-center justify-content-between">
                <h3 class="pure-card-title m-0">Attendance</h3>
                @php
                    $attended  = $meeting->attendees->where('status','attended')->count();
                    $total_att = $meeting->attendees->count();
                @endphp
                <span style="font-size:11px;color:#64748b;">
                    {{ $attended }}/{{ $total_att }} attended
                </span>
            </div>
            <div class="pure-card-body p-0">
                @forelse($meeting->attendees as $attendee)
                <div class="att-row">
                    <div class="att-avatar">
                        {{ strtoupper(substr($attendee->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <p class="mb-0 small fw-medium text-truncate">{{ $attendee->user?->name ?? '—' }}</p>
                        <small class="text-custom-paragraph">{{ $attendee->user?->role?->name ?? '' }}</small>
                    </div>
                    <select name="attendees[{{ $attendee->id }}][status]"
                        class="form-select form-select-sm flex-shrink-0" style="width:110px;">
                        @foreach(['invited','confirmed','attended','absent','declined'] as $s)
                            <option value="{{ $s }}"
                                @selected(old("attendees.{$attendee->id}.status", $attendee->status) === $s)>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @empty
                <div class="px-4 py-3 text-custom-paragraph small fst-italic">No attendees added.</div>
                @endforelse
            </div>
        </div>

        {{-- Save button (sticky) --}}
        <div class="pure-card rounded-custom card-bg shadow-custom sticky-top" style="top:80px;">
            <div class="pure-card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-medium text-custom-paragraph">Completion</span>
                    <span class="small fw-bold text-primary">{{ $pct }}%</span>
                </div>
                <div class="minutes-progress mb-3">
                    <div class="minutes-progress-bar" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-custom-paragraph small mb-0">
                    Notes save per memo. Re-save anytime to update.
                </p>
            </div>
            <div class="pure-card-footer d-flex flex-column gap-2">
                <button type="submit"
                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Save All Minutes
                </button>
                <a href="{{ route('meetings.export.minutes', $meeting) }}" target="_blank"
                   class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M12 15V3M12 15L8 11M12 15L16 11M3 17V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V17"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('meetings.print.minutes', $meeting) }}" target="_blank"
                   class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9V2H18V9M6 18H4C2.89543 18 2 17.1046 2 16V11C2 9.89543 2.89543 9 4 9H20C21.1046 9 22 9.89543 22 11V16C22 17.1046 21.1046 18 20 18H18M6 14H18V22H6V14Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Print Minutes
                </a>
            </div>
        </div>

    </div>

</div>
</form>

@push('scripts')
<script>
// ── Green textarea on content ──────────────────────────────────────
document.querySelectorAll('.mn-note-textarea').forEach(ta => {
    const toggle = () => ta.classList.toggle('has-content', ta.value.trim().length > 0);
    ta.addEventListener('input', toggle);
    toggle();
});

// ── Auto-save on blur (per memo via AJAX) ──────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const MID  = {{ $meeting->id }};

document.querySelectorAll('.mn-note-textarea').forEach(ta => {
    let _lastVal = ta.value;

    ta.addEventListener('blur', async () => {
        const cur = ta.value;
        if (cur === _lastVal) return;   // nothing changed
        _lastVal = cur;

        const memoId = ta.dataset.memoId;
        try {
            const res = await fetch('/meetings/' + MID + '/memos/' + memoId + '/minutes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ minutes_note: cur }),
            });
            if (res.ok) {
                const indicator = document.getElementById('saved-' + memoId);
                if (indicator) {
                    indicator.classList.add('show');
                    setTimeout(() => indicator.classList.remove('show'), 2500);
                }
                ta.classList.toggle('has-content', cur.trim().length > 0);
            }
        } catch {}
    });
});
</script>
@endpush

@endsection
