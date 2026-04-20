{{-- ============================================================ --}}
{{-- ATTENDANCE PDF: resources/views/reports/pdf/attendance.blade.php --}}
{{-- ============================================================ --}}

{{-- Save this file as: resources/views/reports/pdf/attendance.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#1a1a2e; }
    .header { background:#1a1a2e; color:#fff; padding:14px 20px; margin-bottom:14px; }
    .header h1 { font-size:15px; font-weight:700; }
    .header .meta { font-size:9px; color:#a0aec0; margin-top:3px; }

    h3.section-label { font-size:10px; text-transform:uppercase; letter-spacing:0.8px;
        background:#f1f5f9; color:#475569; padding:5px 10px; margin:0 20px 8px; border-left:3px solid #3b82f6; }

    table { width:calc(100% - 40px); margin:0 20px 16px; border-collapse:collapse; font-size:9px; }
    th { padding:5px 7px; background:#f8fafc; text-align:left; font-size:8px; font-weight:700; color:#475569;
        text-transform:uppercase; border-bottom:2px solid #e2e8f0; }
    td { padding:5px 7px; border-bottom:1px solid #f1f5f9; }
    tr:nth-child(even) td { background:#fafafa; }

    .rate-bar-wrap { background:#e2e8f0; border-radius:3px; height:7px; width:80px; display:inline-block; vertical-align:middle; margin-right:5px; }
    .rate-bar { background:#10b981; height:7px; border-radius:3px; display:block; }
    .rate-bar.low { background:#ef4444; }
    .rate-bar.mid { background:#f59e0b; }

    .footer { position:fixed; bottom:0; left:0; right:0; padding:5px 20px; font-size:8px; color:#94a3b8;
        border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; }
</style>
</head>
<body>

<div class="header">
    <h1>Attendance Report</h1>
    <div class="meta">Generated: {{ now()->format('d F Y, H:i') }}
        @if($request->filled('date_from') || $request->filled('date_to'))
        &nbsp;|&nbsp; Period: {{ $request->date_from ?? '—' }} – {{ $request->date_to ?? 'Present' }}
        @endif
    </div>
</div>

{{-- Per-Meeting --}}
<h3 class="section-label">Per Meeting Breakdown</h3>
<table>
    <thead>
        <tr>
            <th>#</th><th>Meeting</th><th>Date</th><th>Total Invited</th><th>Present</th><th>Absent</th><th>Rate</th>
        </tr>
    </thead>
    <tbody>
        @forelse($meetings as $i => $m)
        @php $rate = $m->attendees_count > 0 ? round(($m->present_count / $m->attendees_count)*100) : 0; @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td><b>{{ $m->meeting_number }}</b> {{ Str::limit($m->title,35) }}</td>
            <td>{{ $m->date?->format('d M Y') }}</td>
            <td style="text-align:center">{{ $m->attendees_count }}</td>
            <td style="text-align:center;color:#10b981;font-weight:600">{{ $m->present_count }}</td>
            <td style="text-align:center;color:#ef4444;font-weight:600">{{ $m->absent_count }}</td>
            <td>
                <span class="rate-bar-wrap"><span class="rate-bar {{ $rate < 50 ? 'low' : ($rate < 75 ? 'mid' : '') }}" style="width:{{ $rate }}%"></span></span>
                {{ $rate }}%
            </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:16px">No data.</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Per-User --}}
<h3 class="section-label">Per User Summary</h3>
<table>
    <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Meetings Invited</th><th>Attended</th><th>Rate</th></tr>
    </thead>
    <tbody>
        @forelse($userAttendance as $i => $u)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $u->user?->name ?? '—' }}</td>
            <td style="color:#718096">{{ $u->user?->email ?? '—' }}</td>
            <td style="text-align:center">{{ $u->total_invited }}</td>
            <td style="text-align:center;color:#10b981;font-weight:600">{{ $u->total_present }}</td>
            <td>
                <span class="rate-bar-wrap"><span class="rate-bar {{ $u->attendance_rate < 50 ? 'low' : ($u->attendance_rate < 75 ? 'mid' : '') }}" style="width:{{ $u->attendance_rate }}%"></span></span>
                {{ $u->attendance_rate }}%
            </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:16px">No data.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; Confidential</span>
    <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
</div>
</body>
</html>
