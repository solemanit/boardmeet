{{-- ================================================================== --}}
{{-- MEMBER ACTIVITY PDF                                                 --}}
{{-- Save as: resources/views/reports/pdf/member-activity.blade.php     --}}
{{-- ================================================================== --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:DejaVu Sans,sans-serif; font-size:10px; color:#1a1a2e; }
    .header { background:#1a1a2e;color:#fff;padding:14px 20px;margin-bottom:14px; }
    .header h1 { font-size:15px;font-weight:700; }
    .header .meta { font-size:9px;color:#a0aec0;margin-top:3px; }
    table { width:calc(100% - 40px);margin:0 20px;border-collapse:collapse;font-size:9px; }
    th { padding:5px 7px;background:#f8fafc;text-align:left;font-size:8px;font-weight:700;
        color:#475569;text-transform:uppercase;border-bottom:2px solid #e2e8f0; }
    td { padding:5px 7px;border-bottom:1px solid #f1f5f9; }
    tr:nth-child(even) td { background:#fafafa; }
    .footer { position:fixed;bottom:0;left:0;right:0;padding:5px 20px;font-size:8px;
        color:#94a3b8;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between; }
    .rate-bar-wrap { background:#e2e8f0;border-radius:3px;height:6px;width:60px;display:inline-block;vertical-align:middle;margin-right:4px; }
    .rate-bar { background:#10b981;height:6px;border-radius:3px;display:block; }
    .rate-bar.low { background:#ef4444; }
    .rate-bar.mid { background:#f59e0b; }
</style>
</head>
<body>
<div class="header">
    <h1>Member Activity Report</h1>
    <div class="meta">Generated: {{ now()->format('d F Y, H:i') }}
        @if($request->filled('department')) &nbsp;|&nbsp; Department: {{ $request->department }} @endif
        @if($request->filled('status')) &nbsp;|&nbsp; Status: {{ ucfirst($request->status) }} @endif
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th><th>Name</th><th>Designation</th><th>Department</th><th>Email</th>
            <th>Meetings</th><th>Attended</th><th>Rate</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $i => $m)
        @php
            $att = $attendanceSummary[$m->email] ?? null;
            $total   = $att?->total_meetings ?? 0;
            $attended = $att?->attended ?? 0;
            $rate    = $total > 0 ? round(($attended / $total) * 100) : 0;
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td><b>{{ $m->name }}</b></td>
            <td style="color:#6b7280">{{ $m->designation ?? '—' }}</td>
            <td style="color:#6b7280">{{ $m->department ?? '—' }}</td>
            <td style="color:#6b7280">{{ $m->email ?? '—' }}</td>
            <td style="text-align:center">{{ $total }}</td>
            <td style="text-align:center;color:#10b981;font-weight:600">{{ $attended }}</td>
            <td>
                @if($total > 0)
                <span class="rate-bar-wrap"><span class="rate-bar {{ $rate < 50 ? 'low' : ($rate < 75 ? 'mid' : '') }}" style="width:{{ $rate }}%"></span></span>
                {{ $rate }}%
                @else
                <span style="color:#94a3b8">N/A</span>
                @endif
            </td>
            <td>{{ ucfirst($m->status) }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:16px">No members found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; Confidential</span>
    <span>Total: {{ $members->count() }} members &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }}</span>
</div>
</body>
</html>
