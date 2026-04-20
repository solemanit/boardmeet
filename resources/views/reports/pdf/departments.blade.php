{{-- ================================================================== --}}
{{-- DEPARTMENTS PDF                                                     --}}
{{-- Save as: resources/views/reports/pdf/departments.blade.php         --}}
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
    h3.sec { font-size:10px;text-transform:uppercase;letter-spacing:.8px;background:#f1f5f9;
        color:#475569;padding:5px 10px;margin:0 20px 8px;border-left:3px solid #6366f1; }
    table { width:calc(100% - 40px);margin:0 20px 16px;border-collapse:collapse;font-size:9px; }
    th { padding:5px 7px;background:#f8fafc;text-align:left;font-size:8px;font-weight:700;
        color:#475569;text-transform:uppercase;border-bottom:2px solid #e2e8f0; }
    td { padding:5px 7px;border-bottom:1px solid #f1f5f9; }
    tr:nth-child(even) td { background:#fafafa; }
    .dept-header td { background:#e0e7ff;font-weight:700;font-size:10px;color:#3730a3; }
    .footer { position:fixed;bottom:0;left:0;right:0;padding:5px 20px;font-size:8px;
        color:#94a3b8;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between; }
</style>
</head>
<body>
<div class="header">
    <h1>Department-wise Report</h1>
    <div class="meta">Generated: {{ now()->format('d F Y, H:i') }}</div>
</div>

<h3 class="sec">Department Summary</h3>
<table>
    <thead>
        <tr><th>#</th><th>Department</th><th>Total Members</th><th>Active</th><th>Inactive</th></tr>
    </thead>
    <tbody>
        @forelse($departmentStats as $i => $dept)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><b>{{ $dept->department ?: 'Unassigned' }}</b></td>
            <td style="text-align:center">{{ $dept->total }}</td>
            <td style="text-align:center;color:#10b981;font-weight:600">{{ $dept->active_count }}</td>
            <td style="text-align:center;color:#ef4444;font-weight:600">{{ $dept->inactive_count }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:16px">No data.</td></tr>
        @endforelse
    </tbody>
</table>

<h3 class="sec">Members by Department</h3>
<table>
    <thead>
        <tr><th>Name</th><th>Designation</th><th>Email</th><th>Status</th></tr>
    </thead>
    <tbody>
        @forelse($byDepartment as $dept => $members)
        <tr class="dept-header">
            <td colspan="4">{{ $dept ?: 'Unassigned' }} ({{ $members->count() }})</td>
        </tr>
        @foreach($members as $m)
        <tr>
            <td>{{ $m->name }}</td>
            <td style="color:#6b7280">{{ $m->designation ?? '—' }}</td>
            <td style="color:#6b7280">{{ $m->email ?? '—' }}</td>
            <td>{{ ucfirst($m->status) }}</td>
        </tr>
        @endforeach
        @empty
        <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:16px">No members found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; Confidential</span>
    <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
</div>
</body>
</html>
