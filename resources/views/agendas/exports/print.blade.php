<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Print — Agenda List</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 12px;
        color: #1a202c;
        background: #fff;
        padding: 24px;
    }

    /* ── Print toolbar (hidden when printing) ── */
    .print-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #1e3a5f;
        color: #fff;
        padding: 10px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        gap: 12px;
    }
    .print-toolbar span { font-size: 13px; font-weight: 600; }
    .toolbar-actions { display: flex; gap: 8px; }
    .btn-print {
        background: #fff;
        color: #1e3a5f;
        border: none;
        padding: 6px 16px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-print:hover { background: #e2e8f0; }
    .btn-close-print {
        background: transparent;
        color: rgba(255,255,255,0.7);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-close-print:hover { color: #fff; border-color: #fff; }

    /* ── Header ── */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 14px;
        border-bottom: 2px solid #1e3a5f;
        margin-bottom: 14px;
    }
    .header-left h1 {
        font-size: 20px;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 2px;
    }
    .header-left p { font-size: 11px; color: #718096; }
    .header-right { text-align: right; font-size: 11px; color: #4a5568; line-height: 1.7; }

    /* ── Active Filters ── */
    .filters-bar {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 6px 12px;
        margin-bottom: 12px;
        font-size: 11px;
        color: #4a5568;
    }

    /* ── Summary ── */
    .summary {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
    }
    .s-box {
        flex: 1;
        border-radius: 5px;
        padding: 10px;
        text-align: center;
        color: #fff;
    }
    .s-box .n { font-size: 20px; font-weight: 700; }
    .s-box .l { font-size: 10px; opacity: 0.85; }
    .sb-total    { background: #1e3a5f; }
    .sb-draft    { background: #718096; }
    .sb-pending  { background: #d69e2e; }
    .sb-approved { background: #38a169; }
    .sb-rejected { background: #e53e3e; }

    /* ── Table ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }
    thead tr { background: #1e3a5f; color: #fff; }
    thead th {
        padding: 9px 10px;
        text-align: left;
        font-weight: 600;
        white-space: nowrap;
    }
    tbody tr:nth-child(even) { background: #f7fafc; }
    tbody tr { border-bottom: 1px solid #e8edf2; }
    tbody td { padding: 8px 10px; vertical-align: middle; }

    .pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }
    .pill-draft     { background: #edf2f7; color: #4a5568; }
    .pill-pending   { background: #fffbeb; color: #92400e; }
    .pill-approved  { background: #f0fff4; color: #276749; }
    .pill-rejected  { background: #fff5f5; color: #9b2c2c; }
    .pill-revised   { background: #ebf8ff; color: #2b6cb0; }
    .pill-low       { background: #edf2f7; color: #4a5568; }
    .pill-medium    { background: #ebf8ff; color: #2b6cb0; }
    .pill-high      { background: #fffbeb; color: #92400e; }
    .pill-urgent    { background: #fff5f5; color: #9b2c2c; }

    .agenda-num { font-family: 'Courier New', monospace; font-weight: 700; color: #1e3a5f; }
    .text-muted { color: #718096; }

    /* ── Footer ── */
    .footer {
        margin-top: 16px;
        padding-top: 10px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #718096;
    }

    /* ── Print media ── */
    @media print {
        body { padding: 0; }
        .print-toolbar { display: none !important; }
        .summary { break-inside: avoid; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }

        @page {
            size: A4 landscape;
            margin: 15mm 12mm;
        }
    }
</style>
</head>
<body>

{{-- Toolbar (screen only) --}}
<div class="print-toolbar">
    <span>🖨 Agenda List — Print Preview</span>
    <div class="toolbar-actions">
        <button class="btn-print" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V2H18V9M6 18H4C3 18 2 17 2 16V11C2 10 3 9 4 9H20C21 9 22 10 22 11V16C22 17 21 18 20 18H18M6 14H18V22H6V14Z"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            Print Now
        </button>
        <a href="{{ route('agendas.index') }}" class="btn-close-print">← Back</a>
    </div>
</div>

{{-- Header --}}
<div class="header">
    <div class="header-left">
        <h1>Agenda Management Report</h1>
        <p>Board Meeting System &mdash; Agenda List</p>
    </div>
    <div class="header-right">
        Exported by: <strong>{{ auth()->user()->name }}</strong><br>
        Date: {{ $exportedAt }}<br>
        Total Records: <strong>{{ $agendas->count() }}</strong>
    </div>
</div>

{{-- Filters --}}
@php $activeFilters = array_filter($filters ?? []); @endphp
@if(count($activeFilters))
<div class="filters-bar">
    <strong>Active Filters:</strong>
    @foreach($activeFilters as $key => $val)
        {{ ucfirst($key) }}: <strong>{{ ucfirst(str_replace('_', ' ', $val)) }}</strong>
        @if(!$loop->last) &bull; @endif
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
    <div class="s-box sb-total"><div class="n">{{ $total }}</div><div class="l">Total</div></div>
    <div class="s-box sb-draft"><div class="n">{{ $draft }}</div><div class="l">Draft</div></div>
    <div class="s-box sb-pending"><div class="n">{{ $pending }}</div><div class="l">Pending</div></div>
    <div class="s-box sb-approved"><div class="n">{{ $approved }}</div><div class="l">Approved</div></div>
    <div class="s-box sb-rejected"><div class="n">{{ $rejected }}</div><div class="l">Rejected</div></div>
</div>

{{-- Table --}}
<table>
    <thead>
        <tr>
            <th style="width:28px">#</th>
            <th style="width:115px">Agenda No.</th>
            <th>Title</th>
            <th style="width:72px">Priority</th>
            <th style="width:82px">Category</th>
            <th style="width:80px">Status</th>
            <th style="width:95px">Created By</th>
            <th style="width:82px">Meeting Date</th>
            <th style="width:32px">Ver.</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agendas as $i => $agenda)
        <tr>
            <td class="text-muted">{{ $i + 1 }}</td>
            <td><span class="agenda-num">{{ $agenda->agenda_number }}</span></td>
            <td>
                {{ Str::limit($agenda->title, 65) }}
                @if($agenda->description)
                    <br><span class="text-muted" style="font-size:10px;">{{ Str::limit($agenda->description, 85) }}</span>
                @endif
            </td>
            <td>
                <span class="pill pill-{{ $agenda->priority }}">{{ ucfirst($agenda->priority) }}</span>
            </td>
            <td>{{ $agenda->category ? ucfirst($agenda->category) : '—' }}</td>
            <td>
                @php
                    $pillClass = match($agenda->status) {
                        'draft'          => 'pill-draft',
                        'pending_review' => 'pill-pending',
                        'approved'       => 'pill-approved',
                        'rejected'       => 'pill-rejected',
                        'revised'        => 'pill-revised',
                        default          => 'pill-draft',
                    };
                @endphp
                <span class="pill {{ $pillClass }}">
                    {{ ucfirst(str_replace('_', ' ', $agenda->status)) }}
                </span>
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
    <span>{{ $agendas->count() }} record(s) printed</span>
    <span>Generated: {{ $exportedAt }}</span>
</div>

<script>
    // Auto-trigger print dialog (optional — remove if not wanted)
    // window.addEventListener('load', () => setTimeout(() => window.print(), 800));
</script>

</body>
</html>
