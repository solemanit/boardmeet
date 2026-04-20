{{-- ================================================================== --}}
{{-- AGENDAS / IMPLEMENTATION PDF                                        --}}
{{-- Save as: resources/views/reports/pdf/agendas.blade.php             --}}
{{-- ================================================================== --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:DejaVu Sans,sans-serif; font-size:9.5px; color:#1a1a2e; }
    .header { background:#1a1a2e;color:#fff;padding:14px 20px;margin-bottom:12px; }
    .header h1 { font-size:15px;font-weight:700; }
    .header .meta { font-size:9px;color:#a0aec0;margin-top:3px; }
    .summary { display:flex;gap:8px;margin:0 20px 12px; }
    .sbox { flex:1;border:1px solid #e2e8f0;border-radius:4px;padding:7px 8px;text-align:center; }
    .sbox .v { font-size:16px;font-weight:700; }
    .sbox .l { font-size:8px;color:#718096;margin-top:1px; }
    table { width:calc(100% - 40px);margin:0 20px;border-collapse:collapse;font-size:9px; }
    th { padding:5px 7px;background:#f8fafc;text-align:left;font-size:8px;font-weight:700;
        color:#475569;text-transform:uppercase;border-bottom:2px solid #e2e8f0; }
    td { padding:5px 7px;border-bottom:1px solid #f1f5f9;vertical-align:top; }
    tr:nth-child(even) td { background:#fafafa; }
    .badge { display:inline-block;padding:1px 6px;border-radius:20px;font-size:7.5px;font-weight:600; }
    .b-draft    { background:#e2e8f0;color:#475569; }
    .b-pending  { background:#fef3c7;color:#92400e; }
    .b-approved { background:#d1fae5;color:#065f46; }
    .b-rejected { background:#fee2e2;color:#991b1b; }
    .b-revised  { background:#dbeafe;color:#1d4ed8; }
    .b-low      { background:#e2e8f0;color:#475569; }
    .b-medium   { background:#dbeafe;color:#1d4ed8; }
    .b-high     { background:#fef3c7;color:#92400e; }
    .b-urgent   { background:#fee2e2;color:#991b1b; }
    .footer { position:fixed;bottom:0;left:0;right:0;padding:5px 20px;font-size:8px;
        color:#94a3b8;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between; }
</style>
</head>
<body>
<div class="header">
    <h1>Agenda &amp; Implementation Report</h1>
    <div class="meta">Generated: {{ now()->format('d F Y, H:i') }}
        @if($request->filled('date_from') || $request->filled('date_to'))
        &nbsp;|&nbsp; Meeting Date: {{ $request->date_from ?? '—' }} – {{ $request->date_to ?? 'Present' }}
        @endif
    </div>
</div>

<div class="summary">
    <div class="sbox"><div class="v" style="color:#3b82f6">{{ $summary['total'] }}</div><div class="l">TOTAL</div></div>
    <div class="sbox"><div class="v" style="color:#6b7280">{{ $summary['draft'] }}</div><div class="l">DRAFT</div></div>
    <div class="sbox"><div class="v" style="color:#f59e0b">{{ $summary['pending_review'] }}</div><div class="l">PENDING</div></div>
    <div class="sbox"><div class="v" style="color:#10b981">{{ $summary['approved'] }}</div><div class="l">APPROVED</div></div>
    <div class="sbox"><div class="v" style="color:#ef4444">{{ $summary['rejected'] }}</div><div class="l">REJECTED</div></div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th><th>Agenda No.</th><th>Title</th><th>Priority</th>
            <th>Meeting Date</th><th>Assigned To</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($agendas as $i => $agenda)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><b>{{ $agenda->agenda_number }}</b></td>
            <td>{{ Str::limit($agenda->title, 50) }}</td>
            <td><span class="badge b-{{ $agenda->priority }}">{{ ucfirst($agenda->priority) }}</span></td>
            <td>{{ $agenda->meeting_date?->format('d M Y') ?? '—' }}</td>
            <td style="color:#6b7280">{{ $agenda->assignee?->name ?? '—' }}</td>
            <td><span class="badge b-{{ str_replace('_','-',$agenda->status) }}">{{ ucfirst(str_replace('_',' ',$agenda->status)) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:16px">No agendas found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; Confidential</span>
    <span>Total: {{ $agendas->count() }} &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }}</span>
</div>
</body>
</html>

{{-- ================================================================== --}}
{{-- AUDIT TRAIL PDF                                                     --}}
{{-- Save as: resources/views/reports/pdf/audit-trail.blade.php         --}}
{{-- ================================================================== --}}
