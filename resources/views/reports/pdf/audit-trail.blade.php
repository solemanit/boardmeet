<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:DejaVu Sans,sans-serif; font-size:9px; color:#1a1a2e; }
    .header { background:#7f1d1d;color:#fff;padding:14px 20px;margin-bottom:12px; }
    .header h1 { font-size:15px;font-weight:700; }
    .header .meta { font-size:8.5px;color:#fca5a5;margin-top:3px; }
    .warning { background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:8.5px;
        padding:7px 12px;margin:0 20px 12px;border-radius:4px; }
    table { width:calc(100% - 40px);margin:0 20px;border-collapse:collapse;font-size:8.5px; }
    th { padding:5px 6px;background:#f8fafc;text-align:left;font-size:7.5px;font-weight:700;
        color:#475569;text-transform:uppercase;border-bottom:2px solid #e2e8f0; }
    td { padding:4px 6px;border-bottom:1px solid #f1f5f9;vertical-align:top; }
    tr:nth-child(even) td { background:#fafafa; }
    .footer { position:fixed;bottom:0;left:0;right:0;padding:5px 20px;font-size:8px;
        color:#94a3b8;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between; }
</style>
</head>
<body>

<div class="header">
    <h1>🔒 Audit Trail</h1>
    <div class="meta">Generated: {{ now()->format('d F Y, H:i') }}
        @if($request->filled('date_from') || $request->filled('date_to'))
        &nbsp;|&nbsp; Period: {{ $request->date_from ?? '—' }} – {{ $request->date_to ?? 'Present' }}
        @endif
        &nbsp;|&nbsp; Records: {{ $logs->count() }}
    </div>
</div>

<div class="warning">⚠ This document contains sensitive audit information. Handle with strict confidentiality.</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date &amp; Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Description</th>
            <th>IP Address</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $i => $log)
        <tr>
            <td>{{ $i+1 }}</td>
            <td style="white-space:nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
            <td>
                <b>{{ $log->user?->name ?? 'System' }}</b><br>
                <span style="color:#718096">{{ $log->user?->email ?? '' }}</span>
            </td>
            <td style="font-family:monospace;color:#6366f1">{{ $log->action }}</td>
            <td style="color:#4b5563;max-width:200px">{{ Str::limit($log->description ?? '—', 70) }}</td>
            <td style="font-family:monospace;color:#94a3b8">{{ $log->ip_address ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:16px">No audit logs found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; RESTRICTED &mdash; Audit Trail</span>
    <span>{{ now()->format('d M Y, H:i') }}</span>
</div>
</body>
</html>
