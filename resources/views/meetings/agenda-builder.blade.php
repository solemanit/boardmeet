{{--
    views/meetings/agenda-builder.blade.php
    Fully dynamic — every action persisted via real AJAX API calls
--}}
@extends('layouts.app')
@section('page-title', 'Agenda Builder — ' . $meeting->meeting_number)
@section('title', 'Agenda Builder')

@push('styles')
<style>
:root {
    --ab-radius: 10px;
    --ab-shadow: 0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    --ab-section-bg: #fff;
    --ab-section-header: #f8fafc;
    --ab-section-border: #e2e8f0;
    --ab-memo-bg: #fafbfc;
    --ab-memo-border: #eaeff5;
    --vr-bg:#fffbeb; --vr-c:#92400e; --vr-b:#fde68a;
    --vo-bg:#f0f9ff; --vo-c:#0369a1; --vo-b:#bae6fd;
    --vn-bg:#f1f5f9; --vn-c:#64748b; --vn-b:#e2e8f0;
}

/* ── Section card ──────────────────────────────────────────────── */
.ab-section {
    background: var(--ab-section-bg);
    border: 1.5px solid var(--ab-section-border);
    border-radius: var(--ab-radius);
    box-shadow: var(--ab-shadow);
    margin-bottom: 18px; overflow: hidden;
}
.ab-section.collapsed .ab-section-body { display: none; }

.ab-section-header {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 16px;
    background: var(--ab-section-header);
    border-bottom: 1.5px solid var(--ab-section-border);
    user-select: none;
}
.s-drag { cursor: grab; color: #b0bec5; flex-shrink: 0; touch-action: none; }
.s-drag:active { cursor: grabbing; }

.s-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; min-width: 26px; border-radius: 50%;
    background: var(--bs-primary,#0d6efd); color: #fff;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.s-title { flex:1; font-weight:700; font-size:14px; color:#1e293b; min-width:0; }
.s-title-input {
    flex:1; font-weight:700; font-size:14px;
    border: 1.5px solid var(--bs-primary,#0d6efd); border-radius:6px;
    padding:3px 10px; outline:none; min-width:0;
}
.s-pill {
    font-size:10px; font-weight:700; background:#f1f5f9; color:#64748b;
    border-radius:20px; padding:2px 10px; flex-shrink:0; white-space:nowrap;
}
.s-actions { display:flex; gap:4px; flex-shrink:0; align-items:center; }

.s-collapse-btn {
    width:28px; height:28px; border-radius:6px; border:none;
    background:transparent; cursor:pointer;
    display:flex; align-items:center; justify-content:center; color:#94a3b8;
    transition: background .1s, color .1s;
}
.s-collapse-btn:hover { background:#e2e8f0; color:#1e293b; }
.s-collapse-btn svg { transition: transform .2s; }
.ab-section.collapsed .s-collapse-btn svg { transform: rotate(-90deg); }

/* ── Memo list ─────────────────────────────────────────────────── */
.ab-memo-list { padding:10px 14px 4px; display:flex; flex-direction:column; gap:8px; min-height:6px; }
.ab-add-memo-row { padding:4px 14px 14px; }
.btn-add-memo {
    width:100%; display:flex; align-items:center; gap:7px;
    padding:8px 14px; background:#f8fafc;
    border:1.5px dashed #cbd5e1; border-radius:7px;
    font-size:12px; font-weight:600; color:#64748b; cursor:pointer;
    transition: background .12s, border-color .12s, color .12s;
}
.btn-add-memo:hover { background:#eff6ff; border-color:var(--bs-primary,#0d6efd); color:var(--bs-primary,#0d6efd); }

/* ── Memo card ─────────────────────────────────────────────────── */
.ab-memo {
    background:var(--ab-memo-bg); border:1.5px solid var(--ab-memo-border);
    border-radius:8px; overflow:hidden; transition: box-shadow .12s;
}
.ab-memo-header { display:flex; align-items:flex-start; gap:9px; padding:10px 12px 10px 10px; }

.m-drag { cursor:grab; color:#cdd6e0; padding-top:3px; flex-shrink:0; touch-action:none; }
.m-drag:active { cursor:grabbing; }

.m-num {
    display:inline-flex; align-items:center; justify-content:center;
    width:21px; height:21px; min-width:21px; border-radius:50%;
    background:#e2e8f0; color:#475569;
    font-size:10px; font-weight:700; flex-shrink:0; margin-top:2px;
}
.m-body { flex:1; min-width:0; }
.m-title { font-weight:600; font-size:13px; color:#1e293b; margin:0; line-height:1.4; }
.m-desc  { font-size:12px; color:#64748b; margin:3px 0 0; white-space:pre-wrap; line-height:1.5; }
.m-meta  { display:flex; align-items:center; gap:6px; margin-top:5px; flex-wrap:wrap; }

.vote-badge {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10px; font-weight:700; border-radius:20px; padding:2px 8px;
    text-transform:uppercase; letter-spacing:.4px; border:1px solid;
}
.vote-badge.required { background:var(--vr-bg); color:var(--vr-c); border-color:var(--vr-b); }
.vote-badge.optional { background:var(--vo-bg); color:var(--vo-c); border-color:var(--vo-b); }
.vote-badge.none     { background:var(--vn-bg); color:var(--vn-c); border-color:var(--vn-b); }

.file-badge { display:inline-flex; align-items:center; gap:3px; font-size:10px; color:#64748b; }

.m-actions { display:flex; gap:3px; flex-shrink:0; align-items:flex-start; padding-top:1px; }

.btn-micro {
    width:27px; height:27px; border-radius:6px; border:1px solid #e2e8f0;
    background:#fff; cursor:pointer;
    display:inline-flex; align-items:center; justify-content:center;
    color:#64748b; transition: background .1s, color .1s, border-color .1s; padding:0;
}
.btn-micro:hover           { background:#f1f5f9; color:#1e293b; }
.btn-micro.danger:hover    { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
.btn-micro.primary:hover   { background:#eff6ff; color:var(--bs-primary,#0d6efd); border-color:#bfdbfe; }

/* ── Files panel ───────────────────────────────────────────────── */
.ab-memo-files { padding:0 12px 10px 40px; display:none; }
.ab-memo-files.open { display:block; }

.file-chip-list { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:8px; }
.file-chip {
    display:inline-flex; align-items:center; gap:5px;
    background:#fff; border:1px solid #e2e8f0; border-radius:6px;
    padding:3px 8px 3px 7px; font-size:11px; color:#374151; font-weight:500;
    max-width:210px;
}
.file-chip-name { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.file-chip-dl  { color:#64748b; font-size:13px; text-decoration:none; flex-shrink:0; line-height:1; }
.file-chip-dl:hover { color:var(--bs-primary,#0d6efd); }
.file-chip-rm  { background:none; border:none; cursor:pointer; color:#94a3b8; font-size:15px; padding:0; flex-shrink:0; line-height:1; }
.file-chip-rm:hover { color:#dc2626; }

/* ── Vote result bar ───────────────────────────────────────────── */
.vote-result {
    display:flex; align-items:center; gap:8px; font-size:11px;
    margin-top:6px; padding-top:6px; border-top:1px solid #f1f5f9;
}
.vote-bar { flex:1; height:6px; border-radius:3px; background:#f1f5f9; overflow:hidden; display:flex; }
.vb-approve { background:#16a34a; height:100%; transition:width .3s; }
.vb-reject  { background:#dc2626; height:100%; transition:width .3s; }
.vb-abstain { background:#94a3b8; height:100%; transition:width .3s; }
.vote-counts { color:#64748b; white-space:nowrap; }
.outcome-badge { font-size:10px; font-weight:700; border-radius:20px; padding:1px 8px; text-transform:uppercase; }
.outcome-passed   { background:#dcfce7; color:#166534; }
.outcome-rejected { background:#fee2e2; color:#991b1b; }
.outcome-tied     { background:#fef9c3; color:#854d0e; }
.outcome-pending  { background:#f1f5f9; color:#64748b; }

/* ── Cast vote row ─────────────────────────────────────────────── */
.cast-vote-row {
    display:flex; align-items:center; gap:6px; flex-wrap:wrap;
    margin-top:6px; padding-top:6px; border-top:1px solid #f1f5f9;
}
.cast-btn {
    display:inline-flex; align-items:center; gap:4px;
    font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px;
    border:1.5px solid; cursor:pointer; background:#fff;
    transition: background .1s, color .1s;
}
.cast-btn.approve { border-color:#16a34a; color:#16a34a; }
.cast-btn.approve:hover,.cast-btn.approve.active { background:#16a34a; color:#fff; }
.cast-btn.reject  { border-color:#dc2626; color:#dc2626; }
.cast-btn.reject:hover,.cast-btn.reject.active   { background:#dc2626; color:#fff; }
.cast-btn.abstain { border-color:#64748b; color:#64748b; }
.cast-btn.abstain:hover,.cast-btn.abstain.active { background:#64748b; color:#fff; }
.my-vote-label { font-size:11px; font-weight:700; color:#16a34a; }

/* ── Add section row ───────────────────────────────────────────── */
.ab-add-section-row {
    display:flex; gap:10px; align-items:center;
    padding:13px 16px; background:#fff;
    border:1.5px dashed #cbd5e1; border-radius:var(--ab-radius);
    cursor:pointer; transition: background .12s, border-color .12s; margin-bottom:20px;
}
.ab-add-section-row:hover { background:#eff6ff; border-color:var(--bs-primary,#0d6efd); }
.ab-add-section-row input {
    flex:1; border:none; background:transparent; outline:none;
    font-size:14px; font-weight:600; color:#1e293b;
}
.ab-add-section-row input::placeholder { color:#94a3b8; font-weight:400; }

/* ── Modal upload zone ─────────────────────────────────────────── */
.upload-zone {
    border:2px dashed #cbd5e1; border-radius:8px; padding:22px;
    text-align:center; cursor:pointer; background:#f8fafc;
    transition: border-color .15s, background .15s; position:relative;
}
.upload-zone:hover,.upload-zone.dragover { border-color:var(--bs-primary,#0d6efd); background:#eff6ff; }
.upload-zone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }

.preview-list { display:flex; flex-direction:column; gap:5px; margin-top:8px; }
.preview-item {
    display:flex; align-items:center; gap:8px;
    padding:5px 10px; background:#f8fafc; border:1px solid #e2e8f0;
    border-radius:6px; font-size:12px;
}
.preview-item .pi-name { flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.preview-item .pi-size { color:#94a3b8; font-size:11px; flex-shrink:0; }
.preview-item .pi-rm   { border:none; background:none; cursor:pointer; color:#94a3b8; font-size:15px; padding:0; }
.preview-item .pi-rm:hover { color:#dc2626; }

/* ── Modal vote select ─────────────────────────────────────────── */
.vote-select { display:flex; gap:8px; }
.vote-opt {
    flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px 10px; border:1.5px solid #e2e8f0; border-radius:7px;
    cursor:pointer; font-size:12px; font-weight:600; color:#64748b;
    background:#f8fafc; transition: border-color .1s, background .1s, color .1s; user-select:none;
}
.vote-opt input { display:none; }
.vote-opt.sel-required { border-color:#f59e0b; background:var(--vr-bg); color:var(--vr-c); }
.vote-opt.sel-optional { border-color:#38bdf8; background:var(--vo-bg); color:var(--vo-c); }
.vote-opt.sel-none     { border-color:#94a3b8; background:var(--vn-bg); color:var(--vn-c); }

/* ── Workflow steps ────────────────────────────────────────────── */
.ab-steps { display:flex; align-items:center; flex-wrap:wrap; gap:4px; margin-bottom:24px; }
.ab-step  { display:flex; align-items:center; gap:7px; }
.ab-step-dot {
    width:26px; height:26px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:700; border:2px solid;
}
.ab-step.done   .ab-step-dot { background:#16a34a; border-color:#16a34a; color:#fff; }
.ab-step.active .ab-step-dot { background:var(--bs-primary,#0d6efd); border-color:var(--bs-primary,#0d6efd); color:#fff; }
.ab-step.todo   .ab-step-dot { background:#fff; border-color:#cbd5e1; color:#94a3b8; }
.ab-step-label { font-size:11px; font-weight:600; }
.ab-step.done   .ab-step-label { color:#16a34a; }
.ab-step.active .ab-step-label { color:var(--bs-primary,#0d6efd); }
.ab-step.todo   .ab-step-label { color:#94a3b8; }
.ab-step-line { flex:1; height:2px; min-width:16px; max-width:50px; background:#e2e8f0; }
.ab-step-line.done { background:#16a34a; }

/* ── Toast ─────────────────────────────────────────────────────── */
.ab-toast {
    position:fixed; bottom:24px; right:24px; z-index:9999;
    background:#1e293b; color:#fff; padding:10px 18px; border-radius:8px;
    font-size:13px; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.2);
    transform: translateY(20px); opacity:0; transition: all .22s;
    max-width:320px;
}
.ab-toast.show { transform:translateY(0); opacity:1; }
.ab-toast.success { background:#16a34a; }
.ab-toast.error   { background:#dc2626; }

/* ── Misc ──────────────────────────────────────────────────────── */
.sortable-ghost  { opacity:.3; }
.empty-memos-msg { text-align:center; padding:18px 0 6px; font-size:12px; color:#94a3b8; }
@media(max-width:991.98px) { .ab-sidebar { position:static !important; } }
</style>
@endpush

@section('content')

{{-- Toast container --}}
<div class="ab-toast" id="abToast"></div>

{{-- Page header --}}
<div class="page-header pb-6 d-flex justify-content-between align-items-center gap-4 flex-wrap">
    <div>
        <h2 class="fw-semibold fs-7 mb-1">Agenda Builder</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('meetings.show', $meeting) }}">{{ $meeting->meeting_number }}</a></li>
                <li class="breadcrumb-item active">Agenda Builder</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('meetings.export.agenda', $meeting) }}" target="_blank"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M12 15V3M12 15L8 11M12 15L16 11M3 17V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V17"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('meetings.minutes', $meeting) }}"
           class="btn btn-primary d-flex align-items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Proceed to Minutes
        </a>
    </div>
</div>

@include('layouts.partials.alerts')

{{-- Workflow steps --}}
<div class="ab-steps mb-6">
    <div class="ab-step done">
        <div class="ab-step-dot">✓</div>
        <span class="ab-step-label d-none d-sm-inline">Create Meeting</span>
    </div>
    <div class="ab-step-line done"></div>
    <div class="ab-step active">
        <div class="ab-step-dot">2</div>
        <span class="ab-step-label">Build Agenda</span>
    </div>
    <div class="ab-step-line"></div>
    <div class="ab-step todo">
        <div class="ab-step-dot">3</div>
        <span class="ab-step-label d-none d-sm-inline">Publish</span>
    </div>
    <div class="ab-step-line"></div>
    <div class="ab-step todo">
        <div class="ab-step-dot">4</div>
        <span class="ab-step-label d-none d-sm-inline">Vote</span>
    </div>
    <div class="ab-step-line"></div>
    <div class="ab-step todo">
        <div class="ab-step-dot">5</div>
        <span class="ab-step-label d-none d-sm-inline">Minutes</span>
    </div>
</div>

<div class="row g-5">

    {{-- ════ Builder Column ════════════════════════════════════════════ --}}
    <div class="col-lg-8">

        <div id="sectionList">

            @forelse($meeting->agendaSections as $si => $section)

            {{-- ═══ SECTION ═══════════════════════════════════════════════ --}}
            <div class="ab-section" data-section-id="{{ $section->id }}">
                <div class="ab-section-header">
                    <span class="s-drag">@include('meetings.partials._drag-icon')</span>
                    <span class="s-num">{{ $si + 1 }}</span>
                    <span class="s-title">{{ $section->title }}</span>
                    <span class="s-pill">{{ $section->memos->count() }} memo{{ $section->memos->count() !== 1 ? 's' : '' }}</span>
                    <div class="s-actions">
                        <button class="btn-micro btn-rename-section" title="Rename">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <button class="btn-micro danger btn-delete-section" title="Delete">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                        <button class="s-collapse-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                </div>

                <div class="ab-section-body">
                    <div class="ab-memo-list" id="memoList-{{ $section->id }}">

                        @forelse($section->memos as $mi => $memo)

                        {{-- ─── MEMO ──────────────────────────────────────── --}}
                        <div class="ab-memo" data-memo-id="{{ $memo->id }}">
                            <div class="ab-memo-header">
                                <span class="m-drag">@include('meetings.partials._drag-icon', ['size' => 11])</span>
                                <span class="m-num">{{ $mi + 1 }}</span>
                                <div class="m-body">
                                    <p class="m-title">{{ $memo->title }}</p>
                                    @if($memo->description)
                                        <p class="m-desc">{{ $memo->description }}</p>
                                    @endif
                                    <div class="m-meta">
                                        @if($memo->vote_required !== 'none')
                                            <span class="vote-badge {{ $memo->vote_required }}" data-vote="{{ $memo->vote_required }}">
                                                @if($memo->vote_required === 'required')
                                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                                    Vote: Required
                                                @else
                                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg>
                                                    Vote: Optional
                                                @endif
                                            </span>
                                        @endif
                                        <span class="file-badge">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5"/></svg>
                                            <span class="file-count">{{ $memo->documents->count() }}</span> file{{ $memo->documents->count() !== 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    {{-- Vote results --}}
                                    @if($memo->vote_required !== 'none')
                                        @php $tally = $memo->voteTally(); $outcome = $memo->voteOutcome(); @endphp
                                        <div class="vote-result" id="voteResult-{{ $memo->id }}">
                                            <div class="vote-bar">
                                                <div class="vb-approve" style="width:{{ $tally['total'] ? round($tally['approve']/$tally['total']*100) : 0 }}%"></div>
                                                <div class="vb-reject"  style="width:{{ $tally['total'] ? round($tally['reject'] /$tally['total']*100) : 0 }}%"></div>
                                                <div class="vb-abstain" style="width:{{ $tally['total'] ? round($tally['abstain']/$tally['total']*100) : 0 }}%"></div>
                                            </div>
                                            <span class="vote-counts">{{ $tally['approve'] }}✓ {{ $tally['reject'] }}✕ {{ $tally['abstain'] }}—</span>
                                            <span class="outcome-badge outcome-{{ $outcome }}">{{ ucfirst($outcome) }}</span>
                                        </div>

                                        {{-- Cast vote --}}
                                        @php $myVote = $memo->myVote ?? null; @endphp
                                        <div class="cast-vote-row" data-memo-id="{{ $memo->id }}">
                                            @if($myVote)
                                                <span class="my-vote-label">✓ {{ ucfirst($myVote->vote) }}</span>
                                            @else
                                                <span style="font-size:11px;color:#64748b;">Your vote:</span>
                                            @endif
                                            <button class="cast-btn approve {{ $myVote?->vote === 'approve' ? 'active' : '' }}" data-vote="approve">✓ Approve</button>
                                            <button class="cast-btn reject  {{ $myVote?->vote === 'reject'  ? 'active' : '' }}" data-vote="reject">✕ Reject</button>
                                            <button class="cast-btn abstain {{ $myVote?->vote === 'abstain' ? 'active' : '' }}" data-vote="abstain">— Abstain</button>
                                        </div>
                                    @endif
                                </div>

                                <div class="m-actions">
                                    <button class="btn-micro primary btn-toggle-files" title="Files">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    </button>
                                    <button class="btn-micro btn-edit-memo" title="Edit">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                    <button class="btn-micro danger btn-delete-memo" title="Delete">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Files panel --}}
                            <div class="ab-memo-files" id="memoFiles-{{ $memo->id }}">
                                <div class="file-chip-list" id="chips-{{ $memo->id }}">
                                    @foreach($memo->documents as $doc)
                                    <span class="file-chip" data-doc-id="{{ $doc->id }}">
                                        <span>{{ $doc->icon_emoji }}</span>
                                        <span class="file-chip-name" title="{{ $doc->original_name }}">{{ $doc->original_name }}</span>
                                        <a href="{{ $doc->download_url }}" target="_blank" class="file-chip-dl" title="Download">↓</a>
                                        <button class="file-chip-rm" data-doc-id="{{ $doc->id }}" title="Remove">×</button>
                                    </span>
                                    @endforeach
                                </div>
                                <label class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mt-1" style="font-size:11px;cursor:pointer;">
                                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                    Upload Files
                                    <input type="file" multiple style="display:none;" class="inline-file-input"
                                        data-memo-id="{{ $memo->id }}"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.svg,.webp">
                                </label>
                            </div>
                        </div>
                        {{-- END MEMO --}}

                        @empty
                        <div class="empty-memos-msg">No memos yet — add one below.</div>
                        @endforelse

                    </div>{{-- end memo-list --}}

                    <div class="ab-add-memo-row">
                        <button class="btn-add-memo" data-section-id="{{ $section->id }}">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Add Memo to "{{ $section->title }}"
                        </button>
                    </div>
                </div>
            </div>
            {{-- END SECTION --}}

            @empty
            <div class="text-center py-5 text-custom-paragraph" id="noSectionsMsg">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" class="mb-3 opacity-40 d-block mx-auto">
                    <path d="M9 12H15M9 16H12M7 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17M7 3C7 3 7 5 12 5C17 5 17 3 17 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="fw-medium mb-1">No sections yet.</p>
                <small>Use the form below to create your first section.</small>
            </div>
            @endforelse

        </div>{{-- end sectionList --}}

        {{-- Add Section Row --}}
        <div class="ab-add-section-row">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none" style="color:#94a3b8;flex-shrink:0;">
                <path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input type="text" id="newSectionTitle" placeholder="New section title (e.g. Financial Review)…" maxlength="120">
            <button class="btn btn-primary btn-sm px-4" id="addSectionBtn">Add Section</button>
        </div>

    </div>{{-- end builder col --}}


    {{-- ════ Sidebar ═══════════════════════════════════════════════════ --}}
    <div class="col-lg-4">
        <div class="pure-card rounded-custom card-bg shadow-custom ab-sidebar sticky-top" style="top:80px;">
            <div class="pure-card-header">
                <h3 class="pure-card-title m-0">Meeting Info</h3>
            </div>
            <div class="pure-card-body">
                <dl class="row g-0 mb-0">
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Number</dt>
                    <dd class="col-7 small pb-3"><code>{{ $meeting->meeting_number }}</code></dd>
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Title</dt>
                    <dd class="col-7 small pb-3">{{ Str::limit($meeting->title, 28) }}</dd>
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Type</dt>
                    <dd class="col-7 small pb-3">
                        <span class="badge {{ $meeting->getTypeBadgeClass() }}">{{ strtoupper($meeting->type) }}</span>
                    </dd>
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Date</dt>
                    <dd class="col-7 small pb-3">{{ $meeting->date->format('d M Y') }}</dd>
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-3">Time</dt>
                    <dd class="col-7 small pb-3">{{ $meeting->start_time }}{{ $meeting->end_time ? ' – '.$meeting->end_time : '' }}</dd>
                    <dt class="col-5 text-custom-paragraph small fw-medium pb-1">Venue</dt>
                    <dd class="col-7 small pb-1">{{ $meeting->venue ?? ($meeting->meeting_link ? 'Virtual' : '—') }}</dd>
                </dl>

                {{-- Live stats --}}
                <div class="border rounded-2 p-3 mt-3 d-flex justify-content-around text-center" style="background:#f8fafc;">
                    <div>
                        <div class="fw-bold fs-5 text-primary" id="cntSections">{{ $meeting->agendaSections->count() }}</div>
                        <div style="font-size:11px;" class="text-custom-paragraph">Sections</div>
                    </div>
                    <div class="border-start border-end px-3">
                        <div class="fw-bold fs-5 text-primary" id="cntMemos">{{ $meeting->agendaSections->sum(fn($s) => $s->memos->count()) }}</div>
                        <div style="font-size:11px;" class="text-custom-paragraph">Memos</div>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-warning" id="cntVotes">{{ $meeting->agendaSections->sum(fn($s) => $s->memos->where('vote_required','required')->count()) }}</div>
                        <div style="font-size:11px;" class="text-custom-paragraph">Votes Req.</div>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-3">
                <div class="alert alert-info small mb-0 d-flex gap-2 align-items-start">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-px">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Drag to reorder. All changes save instantly. When done, click <strong>Proceed to Minutes</strong>.</span>
                </div>
            </div>

            {{-- Vote Link Generator --}}
            <div class="px-4 pb-3 border-top pt-3">
                <h6 class="fw-bold mb-3" style="font-size:13px;color:#1e293b;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="vertical-align:-2px;margin-right:5px;">
                        <path d="M10 13C10.4295 13.5741 10.9774 14.0491 11.6066 14.3929C12.2357 14.7367 12.9315 14.9411 13.6467 14.9923C14.3618 15.0435 15.0796 14.9403 15.7513 14.6897C16.4231 14.4392 17.0331 14.047 17.54 13.54L20.54 10.54C21.4508 9.59695 21.9548 8.33394 21.9434 7.02296C21.932 5.71198 21.4061 4.45791 20.4791 3.53087C19.5521 2.60383 18.298 2.07799 16.987 2.0666C15.676 2.0552 14.413 2.55918 13.47 3.47L11.75 5.18M14 11C13.5705 10.4259 13.0226 9.95087 12.3934 9.60713C11.7642 9.26339 11.0685 9.05891 10.3533 9.00775C9.63816 8.9566 8.92037 9.05975 8.24861 9.31027C7.57685 9.56079 6.96684 9.95296 6.45996 10.46L3.45996 13.46C2.54917 14.403 2.04519 15.6661 2.0566 16.977C2.06801 18.288 2.59385 19.5421 3.52089 20.4691C4.44793 21.3962 5.702 21.922 7.01298 21.9334C8.32396 21.9448 9.58697 21.4408 10.53 20.53L12.24 18.82" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Shareable Vote Link
                </h6>

                <div id="vl-active-box" style="display:none;" class="mb-2">
                    <div class="input-group input-group-sm mb-2">
                        <input type="text" id="vl-url" class="form-control form-control-sm" readonly
                            style="font-size:11px;background:#f8fafc;">
                        <button class="btn btn-outline-secondary btn-sm" id="vl-copy-btn" title="Copy">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M8 4H6C4.89543 4 4 4.89543 4 6V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V18M8 4C8 4 8 6 12 6C16 6 16 4 16 4M8 4C8 3.44772 8.44772 3 9 3H15C15.5523 3 16 3.44772 16 4M16 4H18C19.1046 4 20 4.89543 20 6V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                    <div style="font-size:11px;color:#64748b;" id="vl-meta"></div>
                    <button class="btn btn-sm btn-outline-danger w-100 mt-2" id="vl-revoke-btn" style="font-size:11px;">
                        Revoke Link
                    </button>
                </div>

                <div id="vl-generate-box">
                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <label style="font-size:10px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">Expires (optional)</label>
                            <input type="datetime-local" id="vl-expires" class="form-control form-control-sm" style="font-size:11px;">
                        </div>
                        <div class="col-5">
                            <label style="font-size:10px;font-weight:600;color:#64748b;display:block;margin-bottom:3px;">Max Uses</label>
                            <input type="number" id="vl-maxuses" class="form-control form-control-sm" placeholder="∞" min="1" style="font-size:11px;">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm w-100" id="vl-generate-btn" style="font-size:12px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" style="margin-right:4px;vertical-align:-1px;">
                            <path d="M10 13C10.4295 13.5741 10.9774 14.0491 11.6066 14.3929C12.2357 14.7367 12.9315 14.9411 13.6467 14.9923C14.3618 15.0435 15.0796 14.9403 15.7513 14.6897C16.4231 14.4392 17.0331 14.047 17.54 13.54L20.54 10.54C21.4508 9.59695 21.9548 8.33394 21.9434 7.02296C21.932 5.71198 21.4061 4.45791 20.4791 3.53087C19.5521 2.60383 18.298 2.07799 16.987 2.0666C15.676 2.0552 14.413 2.55918 13.47 3.47L11.75 5.18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Generate Vote Link
                    </button>
                </div>

                <a href="{{ route('meetings.vote-report', $meeting) }}"
                class="btn btn-outline-secondary btn-sm w-100 mt-2 d-flex align-items-center justify-content-center gap-1"
                style="font-size:11px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M9 17H15M9 13H15M9 9H11M5 3H19C19.5523 3 20 3.44772 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44772 3 5 3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    View Vote Report
                </a>
            </div>

            <div class="pure-card-footer d-flex flex-column gap-2">
                <a href="{{ route('meetings.minutes', $meeting) }}"
                   class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Proceed to Minutes
                </a>
                <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary w-100">← Back to Meeting</a>
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════════════════════════════
     ADD / EDIT MEMO MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="memoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="memoModalLabel">Add Memo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mm_memoId">
                <input type="hidden" id="mm_sectionId">

                <div class="mb-4">
                    <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                    <input type="text" id="mm_title" class="form-control"
                        placeholder="e.g. Approve Q3 Budget Proposal" maxlength="255">
                    <div class="invalid-feedback">Title is required.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Description</label>
                    <textarea id="mm_desc" class="form-control" rows="3"
                        placeholder="Context, background, or key discussion points…"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium d-block mb-2">Vote Requirement</label>
                    <div class="vote-select" id="mm_voteSelect">
                        <label class="vote-opt" data-val="required">
                            <input type="radio" name="mm_vote" value="required">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Required
                        </label>
                        <label class="vote-opt" data-val="optional">
                            <input type="radio" name="mm_vote" value="optional">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Optional
                        </label>
                        <label class="vote-opt sel-none" data-val="none">
                            <input type="radio" name="mm_vote" value="none" checked>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            None
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label fw-medium">Attachments</label>
                    <div class="upload-zone" id="mm_uploadZone">
                        <input type="file" id="mm_files" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.svg,.webp">
                        <div style="font-size:26px;margin-bottom:6px;">📎</div>
                        <div style="font-size:13px;font-weight:600;color:#374151;">Drop files or click to browse</div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:3px;">PDF, Word, Excel, PPT, Images — max 10 MB each</div>
                    </div>
                    <div id="mm_filePreview" class="preview-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="mm_saveBtn">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Save Memo
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
// ══════════════════════════════════════════════════════════════════
// CONFIG
// ══════════════════════════════════════════════════════════════════
const MID  = {{ $meeting->id }};
const BASE = '/meetings/' + MID;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Toast ──────────────────────────────────────────────────────────
let _toastTimer = null;
function toast(msg, type = 'success') {
    const el = document.getElementById('abToast');
    el.textContent = msg;
    el.className   = 'ab-toast show ' + type;
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('show'), 3000);
}

// ── API ────────────────────────────────────────────────────────────
async function api(method, url, body = null) {
    const opts = {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    };
    if (body instanceof FormData) {
        opts.body = body;
    } else if (body) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(body);
    }
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Request failed (' + res.status + ')');
    return data;
}

// ── Helpers ────────────────────────────────────────────────────────
const esc = s => { const d = document.createElement('div'); d.appendChild(document.createTextNode(s||'')); return d.innerHTML; };
const fmtSize = b => b >= 1048576 ? (b/1048576).toFixed(1)+' MB' : Math.round(b/1024)+' KB';
const fileEmoji = name => {
    const ext = (name.split('.').pop()||'').toLowerCase();
    return { pdf:'📄', doc:'📝', docx:'📝', xls:'📊', xlsx:'📊', ppt:'📋', pptx:'📋',
        jpg:'🖼', jpeg:'🖼', png:'🖼', gif:'🖼', svg:'🖼', webp:'🖼', bmp:'🖼' }[ext] || '📎';
};

// ══════════════════════════════════════════════════════════════════
// COUNTERS
// ══════════════════════════════════════════════════════════════════
function refreshCounts() {
    const secs = document.querySelectorAll('#sectionList .ab-section');
    let memos = 0, votes = 0;
    secs.forEach((sec, si) => {
        const sm = sec.querySelectorAll('.ab-memo');
        memos += sm.length;
        votes += sec.querySelectorAll('.vote-badge.required').length;
        const pill = sec.querySelector('.s-pill');
        if (pill) pill.textContent = sm.length + ' memo' + (sm.length !== 1 ? 's' : '');
        const num = sec.querySelector('.s-num');
        if (num) num.textContent = si + 1;
        sm.forEach((m, mi) => { const n = m.querySelector('.m-num'); if (n) n.textContent = mi + 1; });
    });
    document.getElementById('cntSections').textContent = secs.length;
    document.getElementById('cntMemos').textContent    = memos;
    document.getElementById('cntVotes').textContent    = votes;
    const msg = document.getElementById('noSectionsMsg');
    if (msg) msg.style.display = secs.length ? 'none' : 'block';
}

// ══════════════════════════════════════════════════════════════════
// SECTIONS
// ══════════════════════════════════════════════════════════════════

// ── Collapse ───────────────────────────────────────────────────────
document.querySelectorAll('.s-collapse-btn').forEach(btn =>
    btn.addEventListener('click', () => btn.closest('.ab-section').classList.toggle('collapsed')));

// ── Rename ─────────────────────────────────────────────────────────
document.querySelectorAll('.btn-rename-section').forEach(btn =>
    btn.addEventListener('click', () => startRename(btn.closest('.ab-section'))));

async function startRename(sec) {
    const span = sec.querySelector('.s-title');
    const sectionId = sec.dataset.sectionId;
    const orig  = span.textContent.trim();

    const input = Object.assign(document.createElement('input'), {
        type: 'text', value: orig, className: 's-title-input',
    });
    span.replaceWith(input);
    input.focus(); input.select();

    const commit = async () => {
        const val = input.value.trim() || orig;
        const newSpan = Object.assign(document.createElement('span'), { className: 's-title', textContent: val });
        input.replaceWith(newSpan);
        updateAddMemoBtnLabel(sec, val);
        if (val !== orig) {
            try {
                await api('PUT', BASE + '/sections/' + sectionId, { title: val });
                toast('Section renamed');
            } catch (e) { toast(e.message, 'error'); }
        }
    };
    input.addEventListener('blur', commit);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); commit(); } });
}

function updateAddMemoBtnLabel(sec, title) {
    const btn = sec.querySelector('.btn-add-memo');
    if (btn) btn.lastChild.textContent = ' Add Memo to "' + title + '"';
}

// ── Delete ─────────────────────────────────────────────────────────
document.querySelectorAll('.btn-delete-section').forEach(btn =>
    btn.addEventListener('click', () => deleteSection(btn.closest('.ab-section'))));

async function deleteSection(sec) {
    if (!confirm('Delete this section and all its memos?')) return;
    const sectionId = sec.dataset.sectionId;
    try {
        await api('DELETE', BASE + '/sections/' + sectionId);
        sec.remove();
        refreshCounts();
        toast('Section deleted');
    } catch (e) { toast(e.message, 'error'); }
}

// ── Add ────────────────────────────────────────────────────────────
document.getElementById('addSectionBtn').addEventListener('click', addSection);
document.getElementById('newSectionTitle').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); addSection(); }
});

async function addSection() {
    const input = document.getElementById('newSectionTitle');
    const title = input.value.trim();
    if (!title) { input.focus(); return; }

    const btn = document.getElementById('addSectionBtn');
    btn.disabled = true; btn.textContent = '…';

    try {
        const res = await api('POST', BASE + '/sections', { title });
        input.value = '';
        renderSection(res.section.id, title);
        toast('Section added');
    } catch (e) {
        toast(e.message, 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Add Section';
        input.focus();
    }
}

function renderSection(id, title) {
    const list = document.getElementById('sectionList');
    const num  = list.querySelectorAll('.ab-section').length + 1;

    const div = document.createElement('div');
    div.className = 'ab-section';
    div.dataset.sectionId = id;
    div.innerHTML = buildSectionHtml(id, title, num);
    list.appendChild(div);

    bindSectionEvents(div);
    initMemoSort(div.querySelector('.ab-memo-list'));
    document.getElementById('noSectionsMsg')?.remove();
    refreshCounts();
}

function buildSectionHtml(id, title, num) {
    const drag = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="5" r="1.6" fill="currentColor"/><circle cx="16" cy="5" r="1.6" fill="currentColor"/><circle cx="8" cy="12" r="1.6" fill="currentColor"/><circle cx="16" cy="12" r="1.6" fill="currentColor"/><circle cx="8" cy="19" r="1.6" fill="currentColor"/><circle cx="16" cy="19" r="1.6" fill="currentColor"/></svg>`;
    return `
    <div class="ab-section-header">
        <span class="s-drag">${drag}</span>
        <span class="s-num">${num}</span>
        <span class="s-title">${esc(title)}</span>
        <span class="s-pill">0 memos</span>
        <div class="s-actions">
            <button class="btn-micro btn-rename-section" title="Rename"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
            <button class="btn-micro danger btn-delete-section" title="Delete"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
            <button class="s-collapse-btn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
    </div>
    <div class="ab-section-body">
        <div class="ab-memo-list" id="memoList-${id}">
            <div class="empty-memos-msg">No memos yet — add one below.</div>
        </div>
        <div class="ab-add-memo-row">
            <button class="btn-add-memo" data-section-id="${id}">
                <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Add Memo to "${esc(title)}"
            </button>
        </div>
    </div>`;
}

function bindSectionEvents(sec) {
    sec.querySelector('.s-collapse-btn')?.addEventListener('click', () => sec.classList.toggle('collapsed'));
    sec.querySelector('.btn-rename-section')?.addEventListener('click', () => startRename(sec));
    sec.querySelector('.btn-delete-section')?.addEventListener('click', () => deleteSection(sec));
    sec.querySelector('.btn-add-memo')?.addEventListener('click', function() { openAddMemo(this.dataset.sectionId); });
}

// ── Sortable: sections ─────────────────────────────────────────────
Sortable.create(document.getElementById('sectionList'), {
    animation: 150, handle: '.s-drag', ghostClass: 'sortable-ghost', draggable: '.ab-section',
    async onEnd() {
        const order = [...document.querySelectorAll('#sectionList .ab-section')].map(s => +s.dataset.sectionId);
        refreshCounts();
        try { await api('POST', BASE + '/sections/reorder', { order }); }
        catch (e) { toast(e.message, 'error'); }
    },
});

// ══════════════════════════════════════════════════════════════════
// MEMOS — Modal
// ══════════════════════════════════════════════════════════════════
const memoModal = new bootstrap.Modal(document.getElementById('memoModal'));
let pendingFiles = [];

// Bind existing "Add Memo" buttons
document.querySelectorAll('.btn-add-memo').forEach(btn =>
    btn.addEventListener('click', function() { openAddMemo(this.dataset.sectionId); }));

function openAddMemo(sectionId) {
    document.getElementById('memoModalLabel').textContent = 'Add Memo';
    document.getElementById('mm_memoId').value   = '';
    document.getElementById('mm_sectionId').value = sectionId;
    document.getElementById('mm_title').value    = '';
    document.getElementById('mm_desc').value     = '';
    document.getElementById('mm_filePreview').innerHTML = '';
    document.getElementById('mm_title').classList.remove('is-invalid');
    pendingFiles = [];
    setVote('none');
    memoModal.show();
    setTimeout(() => document.getElementById('mm_title').focus(), 350);
}

function openEditMemo(memoEl) {
    const sectionEl = memoEl.closest('.ab-section');
    document.getElementById('memoModalLabel').textContent = 'Edit Memo';
    document.getElementById('mm_memoId').value    = memoEl.dataset.memoId;
    document.getElementById('mm_sectionId').value = sectionEl.dataset.sectionId;
    document.getElementById('mm_title').value     = memoEl.querySelector('.m-title').textContent.trim();
    document.getElementById('mm_desc').value      = memoEl.querySelector('.m-desc')?.textContent.trim() || '';
    document.getElementById('mm_filePreview').innerHTML = '';
    document.getElementById('mm_title').classList.remove('is-invalid');
    pendingFiles = [];
    const badge = memoEl.querySelector('.vote-badge');
    setVote(badge?.dataset.vote || 'none');
    memoModal.show();
}

// Vote selector
document.querySelectorAll('#mm_voteSelect .vote-opt').forEach(opt =>
    opt.addEventListener('click', () => {
        opt.querySelector('input').checked = true;
        setVote(opt.dataset.val);
    }));

function setVote(val) {
    document.querySelectorAll('#mm_voteSelect .vote-opt').forEach(o => {
        o.classList.remove('sel-required','sel-optional','sel-none');
        if (o.dataset.val === val) o.classList.add('sel-' + val);
        o.querySelector('input').checked = (o.dataset.val === val);
    });
}

// File drop / select in modal
const uploadZone = document.getElementById('mm_uploadZone');
const filesInput = document.getElementById('mm_files');

uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
uploadZone.addEventListener('drop', e => {
    e.preventDefault(); uploadZone.classList.remove('dragover');
    addPendingFiles(Array.from(e.dataTransfer.files));
});
filesInput.addEventListener('change', () => { addPendingFiles(Array.from(filesInput.files)); filesInput.value = ''; });

function addPendingFiles(files) {
    files.forEach(f => { if (!pendingFiles.find(p => p.name === f.name && p.size === f.size)) pendingFiles.push(f); });
    renderPendingFiles();
}

function renderPendingFiles() {
    const list = document.getElementById('mm_filePreview');
    list.innerHTML = '';
    pendingFiles.forEach((f, i) => {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `<span>${fileEmoji(f.name)}</span>
            <span class="pi-name">${esc(f.name)}</span>
            <span class="pi-size">${fmtSize(f.size)}</span>
            <button class="pi-rm">&times;</button>`;
        div.querySelector('.pi-rm').onclick = () => { pendingFiles.splice(i, 1); renderPendingFiles(); };
        list.appendChild(div);
    });
}

// ── Save memo (Add or Edit) ────────────────────────────────────────
document.getElementById('mm_saveBtn').addEventListener('click', saveMemo);

async function saveMemo() {
    const title     = document.getElementById('mm_title').value.trim();
    const desc      = document.getElementById('mm_desc').value.trim();
    const voteReq   = document.querySelector('#mm_voteSelect input:checked')?.value || 'none';
    const sectionId = document.getElementById('mm_sectionId').value;
    const memoId    = document.getElementById('mm_memoId').value;

    if (!title) {
        document.getElementById('mm_title').classList.add('is-invalid');
        document.getElementById('mm_title').focus();
        return;
    }
    document.getElementById('mm_title').classList.remove('is-invalid');

    const btn = document.getElementById('mm_saveBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

    const fd = new FormData();
    fd.append('title',         title);
    fd.append('description',   desc);
    fd.append('vote_required', voteReq);
    pendingFiles.forEach(f => fd.append('documents[]', f));

    try {
        if (memoId) {
            // Laravel needs POST + _method for file uploads via FormData
            fd.append('_method', 'PUT');
            const res = await api('POST', BASE + '/memos/' + memoId, fd);
            if (res.success) {
                updateMemoInDom(memoId, res.memo);
                toast('Memo updated');
            }
        } else {
            const res = await api('POST', BASE + '/sections/' + sectionId + '/memos', fd);
            if (res.success) {
                renderMemo(sectionId, res.memo);
                toast('Memo added');
            }
        }
        memoModal.hide();
    } catch (e) {
        toast(e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Save Memo';
    }
}

// ══════════════════════════════════════════════════════════════════
// MEMO DOM helpers
// ══════════════════════════════════════════════════════════════════
function renderMemo(sectionId, memo) {
    const list = document.getElementById('memoList-' + sectionId);
    list?.querySelector('.empty-memos-msg')?.remove();
    const div = document.createElement('div');
    div.className = 'ab-memo';
    div.dataset.memoId = memo.id;
    div.innerHTML = buildMemoHtml(memo);
    list?.appendChild(div);
    bindMemoEvents(div);
    refreshCounts();
}

function updateMemoInDom(memoId, memo) {
    const el = document.querySelector('.ab-memo[data-memo-id="' + memoId + '"]');
    if (!el) return;
    el.querySelector('.m-title').textContent = memo.title;
    const descEl = el.querySelector('.m-desc');
    if (descEl) descEl.textContent = memo.description || '';

    // update vote badge
    const meta = el.querySelector('.m-meta');
    el.querySelector('.vote-badge')?.remove();
    if (memo.vote_required !== 'none') {
        const span = document.createElement('span');
        span.className = `vote-badge ${memo.vote_required}`;
        span.dataset.vote = memo.vote_required;
        span.innerHTML = voteBadgeHtml(memo.vote_required);
        meta?.prepend(span);
    }

    // append any new doc chips (server returns fresh memo with documents)
    if (Array.isArray(memo.documents)) {
        const chips = el.querySelector('[id^="chips-"]');
        if (chips) {
            memo.documents.forEach(d => {
                if (!chips.querySelector('[data-doc-id="' + d.id + '"]')) {
                    chips.insertAdjacentHTML('beforeend', buildChipHtml(d));
                }
            });
            bindChipRemovers(chips, memoId);
            updateFileCountBadge(el);
        }
    }
    refreshCounts();
}

function buildMemoHtml(memo) {
    const docs    = (memo.documents||[]).map(buildChipHtml).join('');
    const vBadge  = memo.vote_required !== 'none'
        ? `<span class="vote-badge ${memo.vote_required}" data-vote="${memo.vote_required}">${voteBadgeHtml(memo.vote_required)}</span>` : '';
    const cvRow   = memo.vote_required !== 'none' ? `
        <div class="vote-result" id="voteResult-${memo.id}">
            <div class="vote-bar"><div class="vb-approve" style="width:0%"></div><div class="vb-reject" style="width:0%"></div><div class="vb-abstain" style="width:0%"></div></div>
            <span class="vote-counts">0✓ 0✕ 0—</span>
            <span class="outcome-badge outcome-pending">Pending</span>
        </div>
        <div class="cast-vote-row" data-memo-id="${memo.id}">
            <span style="font-size:11px;color:#64748b;">Your vote:</span>
            <button class="cast-btn approve" data-vote="approve">✓ Approve</button>
            <button class="cast-btn reject"  data-vote="reject">✕ Reject</button>
            <button class="cast-btn abstain" data-vote="abstain">— Abstain</button>
        </div>` : '';
    const dragSvg = `<svg width="11" height="11" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="5" r="1.4" fill="currentColor"/><circle cx="16" cy="5" r="1.4" fill="currentColor"/><circle cx="8" cy="12" r="1.4" fill="currentColor"/><circle cx="16" cy="12" r="1.4" fill="currentColor"/><circle cx="8" cy="19" r="1.4" fill="currentColor"/><circle cx="16" cy="19" r="1.4" fill="currentColor"/></svg>`;

    return `
    <div class="ab-memo-header">
        <span class="m-drag">${dragSvg}</span>
        <span class="m-num"></span>
        <div class="m-body">
            <p class="m-title">${esc(memo.title)}</p>
            <p class="m-desc">${esc(memo.description||'')}</p>
            <div class="m-meta">
                ${vBadge}
                <span class="file-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="1.5"/></svg>
                    <span class="file-count">${(memo.documents||[]).length}</span> files
                </span>
            </div>
            ${cvRow}
        </div>
        <div class="m-actions">
            <button class="btn-micro primary btn-toggle-files" title="Files">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <button class="btn-micro btn-edit-memo" title="Edit">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M11 4H4C3.44772 4 3 4.44772 3 5V20C3 20.5523 3.44772 21 4 21H19C19.5523 21 20 20.5523 20 19V12M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L12 15L8 16L9 12L18.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
            <button class="btn-micro danger btn-delete-memo" title="Delete">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
        </div>
    </div>
    <div class="ab-memo-files" id="memoFiles-${memo.id}">
        <div class="file-chip-list" id="chips-${memo.id}">${docs}</div>
        <label class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 mt-1" style="font-size:11px;cursor:pointer;">
            <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1V15M1 8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Upload Files
            <input type="file" multiple style="display:none;" class="inline-file-input"
                data-memo-id="${memo.id}"
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.svg,.webp">
        </label>
    </div>`;
}

function buildChipHtml(doc) {
    const name = doc.original_name || doc.file_name || 'file';
    const url  = doc.download_url  || '#';
    return `<span class="file-chip" data-doc-id="${doc.id}">
        <span>${fileEmoji(name)}</span>
        <span class="file-chip-name" title="${esc(name)}">${esc(name)}</span>
        <a href="${url}" target="_blank" class="file-chip-dl" title="Download">↓</a>
        <button class="file-chip-rm" data-doc-id="${doc.id}" title="Remove">×</button>
    </span>`;
}

function voteBadgeHtml(val) {
    return {
        required: `<svg width="9" height="9" viewBox="0 0 24 24" fill="none"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Vote: Required`,
        optional: `<svg width="9" height="9" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg> Vote: Optional`,
        none: '',
    }[val] || '';
}

// ── Bind memo events ───────────────────────────────────────────────
document.querySelectorAll('.ab-memo').forEach(bindMemoEvents);

function bindMemoEvents(el) {
    el.querySelector('.btn-toggle-files')?.addEventListener('click', () => {
        const fp = el.querySelector('.ab-memo-files');
        if (fp) {
            fp.classList.toggle('open');
            const svg = el.querySelector('.btn-toggle-files svg');
            if (svg) svg.style.transform = fp.classList.contains('open') ? 'rotate(180deg)' : '';
        }
    });
    el.querySelector('.btn-edit-memo')?.addEventListener('click', () => openEditMemo(el));
    el.querySelector('.btn-delete-memo')?.addEventListener('click', () => deleteMemo(el));
    el.querySelector('.inline-file-input')?.addEventListener('change', async function() {
        await uploadInlineFiles(this, el);
    });
    el.querySelectorAll('.cast-btn').forEach(btn =>
        btn.addEventListener('click', () => castVote(el, btn.dataset.vote)));

    // chip removers already in DOM
    const chips = el.querySelector('[id^="chips-"]');
    if (chips) bindChipRemovers(chips, el.dataset.memoId);
}

// ── Delete memo ────────────────────────────────────────────────────
async function deleteMemo(el) {
    if (!confirm('Remove this memo?')) return;
    const memoId = el.dataset.memoId;
    try {
        await api('DELETE', BASE + '/memos/' + memoId);
        el.remove();
        refreshCounts();
        toast('Memo removed');
    } catch (e) { toast(e.message, 'error'); }
}

// ── Sortable: memo lists ───────────────────────────────────────────
function initMemoSort(list) {
    if (!list) return;
    Sortable.create(list, {
        animation: 150, handle: '.m-drag', ghostClass: 'sortable-ghost', draggable: '.ab-memo',
        group: 'memos',                  // allows cross-section drag
        async onEnd(evt) {
            const toSectionId   = evt.to.id.replace('memoList-', '');
            const fromSectionId = evt.from.id.replace('memoList-', '');
            const memoId        = evt.item.dataset.memoId;

            // Re-number both lists
            refreshCounts();

            // If moved to a different section, call move endpoint
            if (toSectionId !== fromSectionId) {
                try {
                    await api('POST', BASE + '/memos/' + memoId + '/move', {
                        section_id: +toSectionId,
                        order: evt.newIndex + 1,
                    });
                } catch (e) { toast(e.message, 'error'); }
            }

            // Persist new order of the destination list
            const order = [...evt.to.querySelectorAll('.ab-memo')].map(m => +m.dataset.memoId);
            try {
                await api('POST', BASE + '/sections/' + toSectionId + '/memos/reorder', { order });
            } catch (e) { toast(e.message, 'error'); }
        },
    });
}

document.querySelectorAll('.ab-memo-list').forEach(initMemoSort);

// ══════════════════════════════════════════════════════════════════
// DOCUMENTS — Inline upload & remove
// ══════════════════════════════════════════════════════════════════

// Bind already-in-DOM removers
document.querySelectorAll('.file-chip-rm').forEach(btn => {
    const memoEl = btn.closest('.ab-memo');
    if (memoEl) addChipRemoveListener(btn, memoEl.dataset.memoId);
});

function bindChipRemovers(chipsContainer, memoId) {
    chipsContainer.querySelectorAll('.file-chip-rm').forEach(btn => {
        if (!btn._bound) addChipRemoveListener(btn, memoId);
    });
}

function addChipRemoveListener(btn, memoId) {
    btn._bound = true;
    btn.addEventListener('click', async () => {
        const docId = btn.dataset.docId;
        if (!confirm('Delete this file?')) return;
        try {
            await api('DELETE', BASE + '/memos/' + memoId + '/documents/' + docId);
            btn.closest('.file-chip')?.remove();
            const memoEl = document.querySelector('.ab-memo[data-memo-id="' + memoId + '"]');
            if (memoEl) updateFileCountBadge(memoEl);
            toast('File removed');
        } catch (e) { toast(e.message, 'error'); }
    });
}

async function uploadInlineFiles(input, memoEl) {
    const memoId = memoEl.dataset.memoId;
    const chips  = memoEl.querySelector('[id^="chips-"]');

    for (const file of Array.from(input.files)) {
        const fd = new FormData();
        fd.append('file', file);
        try {
            const res = await api('POST', BASE + '/memos/' + memoId + '/documents', fd);
            if (res.success && chips) {
                chips.insertAdjacentHTML('beforeend', buildChipHtml(res.document));
                bindChipRemovers(chips, memoId);
                updateFileCountBadge(memoEl);
                toast('File uploaded');
            }
        } catch (e) { toast('Upload failed: ' + e.message, 'error'); }
    }
    input.value = '';
}

function updateFileCountBadge(memoEl) {
    const chips = memoEl.querySelector('[id^="chips-"]');
    const n     = chips?.querySelectorAll('.file-chip').length || 0;
    const cnt   = memoEl.querySelector('.file-count');
    if (cnt) cnt.textContent = n;
}

// ══════════════════════════════════════════════════════════════════
// VOTING
// ══════════════════════════════════════════════════════════════════
async function castVote(memoEl, vote) {
    const memoId = memoEl.dataset.memoId;
    try {
        const res = await api('POST', BASE + '/memos/' + memoId + '/vote', { vote });
        if (!res.success) { toast(res.message || 'Vote failed', 'error'); return; }

        // Update cast buttons active state
        memoEl.querySelectorAll('.cast-btn').forEach(b => b.classList.remove('active'));
        memoEl.querySelector(`.cast-btn[data-vote="${vote}"]`)?.classList.add('active');

        // Update or create submitted label
        let label = memoEl.querySelector('.my-vote-label');
        if (!label) {
            label = Object.assign(document.createElement('span'), { className: 'my-vote-label' });
            memoEl.querySelector('.cast-vote-row')?.prepend(label);
        }
        label.textContent = '✓ ' + vote.charAt(0).toUpperCase() + vote.slice(1);

        // Refresh vote result bar
        renderVoteBar(memoEl, res.tally, res.outcome);
        toast('Vote recorded');
    } catch (e) { toast(e.message, 'error'); }
}

function renderVoteBar(memoEl, tally, outcome) {
    const row = document.getElementById('voteResult-' + memoEl.dataset.memoId);
    if (!row) return;
    const total = tally.total || 1;
    row.querySelector('.vb-approve').style.width = Math.round(tally.approve/total*100) + '%';
    row.querySelector('.vb-reject') .style.width = Math.round(tally.reject /total*100) + '%';
    row.querySelector('.vb-abstain').style.width = Math.round(tally.abstain/total*100) + '%';
    const cnts = row.querySelector('.vote-counts');
    if (cnts) cnts.textContent = tally.approve + '✓ ' + tally.reject + '✕ ' + tally.abstain + '—';
    const ob = row.querySelector('.outcome-badge');
    if (ob) {
        ob.className = 'outcome-badge outcome-' + outcome;
        ob.textContent = outcome.charAt(0).toUpperCase() + outcome.slice(1);
    }
}

// ══════════════════════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════════════════════
refreshCounts();


// ══════════════════════════════════════════════════════════════════
// VOTE LINK
// ══════════════════════════════════════════════════════════════════
(async function initVoteLink() {
    try {
        const res = await api('GET', BASE + '/vote-link');
        if (res.active) showActiveLink(res);
    } catch {}
})();

function showActiveLink(data) {
    document.getElementById('vl-active-box').style.display   = '';
    document.getElementById('vl-generate-box').style.display = 'none';
    document.getElementById('vl-url').value = data.url;

    let meta = `Used: <strong>${data.use_count}</strong>${data.max_uses ? ' / ' + data.max_uses : ''}`;
    if (data.expires_at) meta += ` · Expires: <strong>${data.expires_at}</strong>`;
    if (data.is_expired) meta += ' <span class="badge bg-secondary" style="font-size:9px;">Expired</span>';
    document.getElementById('vl-meta').innerHTML = meta;
}

function showGenerateBox() {
    document.getElementById('vl-active-box').style.display   = 'none';
    document.getElementById('vl-generate-box').style.display = '';
}

document.getElementById('vl-generate-btn').addEventListener('click', async () => {
    const btn = document.getElementById('vl-generate-btn');
    btn.disabled = true; btn.textContent = '…';
    try {
        const body = {};
        const exp = document.getElementById('vl-expires').value;
        const mu  = document.getElementById('vl-maxuses').value;
        if (exp) body.expires_at = exp;
        if (mu)  body.max_uses   = parseInt(mu);

        const res = await api('POST', BASE + '/vote-link/generate', body);
        if (res.success) {
            showActiveLink({ url: res.url, use_count: 0, max_uses: body.max_uses || null, expires_at: res.expires_at });
            toast('Vote link generated — ready to share!', 'success');
        }
    } catch (e) { toast(e.message, 'error'); }
    finally { btn.disabled = false; btn.textContent = 'Generate Vote Link'; }
});

document.getElementById('vl-revoke-btn').addEventListener('click', async () => {
    if (!confirm('Revoke this link? Voters with the current link can no longer vote.')) return;
    try {
        await api('POST', BASE + '/vote-link/revoke');
        showGenerateBox();
        toast('Vote link revoked');
    } catch (e) { toast(e.message, 'error'); }
});

document.getElementById('vl-copy-btn').addEventListener('click', () => {
    const url = document.getElementById('vl-url').value;
    navigator.clipboard.writeText(url).then(() => toast('Link copied to clipboard!'));
});
</script>
@endpush

@endsection
