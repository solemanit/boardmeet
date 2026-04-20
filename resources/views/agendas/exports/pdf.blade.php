<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Agenda List — {{ now()->format('d M Y') }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11px;
        color: #1a1a2e;
        background: #fff;
    }

    /* ── Header ── */
    .header {
        background: #1e3a5f;
        color: #fff;
        padding: 18px 24px;
        margin-bottom: 20px;
        border-radius: 6px;
    }
    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .header h1 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .header .sub {
        font-size: 10px;
        opacity: 0.75;
        margin-top: 3px;
    }
    .header .meta {
        text-align: right;
        font-size: 10px;
        opacity: 0.80;
        line-height: 1.6;
    }

    /* ── Filters bar ── */
    .filters {
        background: #f0f4f8;
        border: 1px solid #d0dce8;
        border-radius: 4px;
        padding: 7px 14px;
        margin-bottom: 16px;
        font-size: 10px;
        color: #4a5568;
    }
    .filters strong { color: #1e3a5f; }

    /* ── Summary badges ── */
    .summary {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
    }
    .badge-box {
        flex: 1;
        border-radius: 5px;
        padding: 9px 12px;
        text-align: center;
        color: #fff;
    }
    .badge-box .num  { font-size: 18px; font-weight: 700; line-height: 1; }
    .badge-box .lbl  { font-size: 9px; opacity: 0.85; margin-top: 2px; }
    .bb-total   { background: #1e3a5f; }
    .bb-draft   { background: #718096; }
    .bb-pending { background: #d69e2e; }
    .bb-approved{ background: #38a169; }
    .bb-rejected{ background: #e53e3e; }

    /* ── Table ── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    thead tr {
        background: #1e3a5f;
        color: #fff;
    }
    thead th {
        padding: 9px 10px;
        text-align: left;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    tbody tr:nth-child(even) { background: #f7fafc; }
    tbody tr:nth-child(odd)  { background: #fff; }
    tbody tr { border-bottom: 1px solid #e8edf2; }
    tbody td {
        padding: 8px 10px;
        vertical-align: middle;
        font-size: 10px;
        color: #2d3748;
    }

    /* ── Status / Priority pills ── */
    .pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 600;
        white-space: nowrap;
    }
    .pill-draft     { background: #edf2f7; color: #4a5568; }
    .pill-pending   { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .pill-approved  { background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; }
    .pill-rejected  { background: #fff5f5; color: #9b2c2c; border: 1px solid #feb2b2; }
    .pill-revised   { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }

    .pill-low     { background: #edf2f7; color: #4a5568; }
    .pill-medium  { background: #ebf8ff; color: #2b6cb0; }
    .pill-high    { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .pill-urgent  { background: #fff5f5; color: #9b2c2c; border: 1px solid #feb2b2; }

    .agenda-num { font-family: 'Courier New', monospace; font-size: 10px; font-weight: 700; color: #1e3a5f; }
    .text-muted { color: #718096; }

    /* ── Footer ── */
    .footer {
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
        display: flex;
        justify-content: space-between;
        color: #718096;
        font-size: 9px;
    }

    /* ── Page number (dompdf) ── */
    .page-num:after { content: counter(page); }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        <div>
            <h1>Agenda Management Report</h1>
            <div class="sub">Board Meeting System &mdash; Agenda List Export</div>
        </div>
        <div class="meta">
            Exported by: {{ auth()->user()->name }}<br>
            Date: {{ $exportedAt }}<br>
            Total records: {{ $agendas->count() }}
        </div>
    </div>
</div>

{{-- Active Filters --}}
@php
    $activeFilters = array_filter($filters ?? []);
@endphp
@if(count($activeFilters))
<div class="filters">
    <strong>Active Filters:</strong>
    @foreach($activeFilters as $key => $val)
        &nbsp;{{ ucfirst($key) }}: <strong>{{ ucfirst(str_replace('_', ' ', $val)) }}</strong>
        @if(!$loop->last) &nbsp;&bull;&nbsp; @endif
    @endforeach
</div>
@endif

{{-- Summary --}}
@php
    $total    = $agendas->count();
    $draft    = $agendas->where('status', 'draft')->count();
    $pending  = $agendas->where('status', 'pending_review')->count();
    $approved = $agendas->where('status', 'approved')->count();
    $rejected = $agendas->where('status', 'rejected')->count();
@endphp
<div class="summary">
    <div class="badge-box bb-total">
        <div class="num">{{ $total }}</div>
        <div class="lbl">Total</div>
    </div>
    <div class="badge-box bb-draft">
        <div class="num">{{ $draft }}</div>
        <div class="lbl">Draft</div>
    </div>
    <div class="badge-box bb-pending">
        <div class="num">{{ $pending }}</div>
        <div class="lbl">Pending</div>
    </div>
    <div class="badge-box bb-approved">
        <div class="num">{{ $approved }}</div>
        <div class="lbl">Approved</div>
    </div>
    <div class="badge-box bb-rejected">
        <div class="num">{{ $rejected }}</div>
        <div class="lbl">Rejected</div>
    </div>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th style="width:30px">#</th>
            <th style="width:110px">Agenda No.</th>
            <th>Title</th>
            <th style="width:70px">Priority</th>
            <th style="width:80px">Category</th>
            <th style="width:75px">Status</th>
            <th style="width:90px">Created By</th>
            <th style="width:80px">Meeting Date</th>
            <th style="width:30px">Ver.</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agendas as $i => $agenda)
        <tr>
            <td class="text-muted">{{ $i + 1 }}</td>
            <td><span class="agenda-num">{{ $agenda->agenda_number }}</span></td>
            <td>
                {{ Str::limit($agenda->title, 60) }}
                @if($agenda->description)
                    <br><span class="text-muted" style="font-size:9px;">{{ Str::limit($agenda->description, 80) }}</span>
                @endif
            </td>
            <td>
                <span class="pill pill-{{ $agenda->priority }}">{{ ucfirst($agenda->priority) }}</span>
            </td>
            <td>{{ $agenda->category ? ucfirst($agenda->category) : '—' }}</td>
            <td>
                @php
                    $s = $agenda->status;
                    $pillClass = match($s) {
                        'draft'          => 'pill-draft',
                        'pending_review' => 'pill-pending',
                        'approved'       => 'pill-approved',
                        'rejected'       => 'pill-rejected',
                        'revised'        => 'pill-revised',
                        default          => 'pill-draft',
                    };
                @endphp
                <span class="pill {{ $pillClass }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</span>
            </td>
            <td>{{ $agenda->creator?->name ?? '—' }}</td>
            <td>{{ $agenda->meeting_date?->format('d M Y') ?? '—' }}</td>
            <td style="text-align:center;">v{{ $agenda->version }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center; padding:20px; color:#718096; font-style:italic;">
                No agenda records found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Footer --}}
<div class="footer">
    <span>Board Meeting System &mdash; Confidential</span>
    <span>Page <span class="page-num"></span></span>
    <span>Generated: {{ $exportedAt }}</span>
</div>

</body>
</html>
