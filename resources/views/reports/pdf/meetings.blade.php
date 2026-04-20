<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }

    .header { background: #1a1a2e; color: #fff; padding: 16px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 16px; font-weight: 700; letter-spacing: 0.5px; }
    .header .meta { font-size: 9px; color: #a0aec0; margin-top: 3px; }

    .summary { display: flex; gap: 10px; margin: 0 20px 14px; }
    .summary-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; text-align: center; }
    .summary-box .val { font-size: 18px; font-weight: 700; }
    .summary-box .lbl { font-size: 8px; color: #718096; margin-top: 2px; }
    .val-primary  { color: #3b82f6; }
    .val-success  { color: #10b981; }
    .val-danger   { color: #ef4444; }
    .val-info     { color: #6366f1; }

    table { width: calc(100% - 40px); margin: 0 20px; border-collapse: collapse; font-size: 9px; }
    thead tr { background: #f1f5f9; }
    th { padding: 6px 8px; text-align: left; font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.4px; font-size: 8px; }
    td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    tr:nth-child(even) td { background: #fafafa; }

    .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 8px; font-weight: 600; }
    .badge-scheduled { background: #dbeafe; color: #1d4ed8; }
    .badge-completed  { background: #d1fae5; color: #065f46; }
    .badge-cancelled  { background: #fee2e2; color: #991b1b; }
    .badge-ongoing    { background: #fef3c7; color: #92400e; }
    .badge-postponed  { background: #e2e8f0; color: #475569; }

    .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 6px 20px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>

<div class="header">
    <h1>Meeting Report</h1>
    <div class="meta">
        Generated: {{ now()->format('d F Y, H:i') }}
        @if($request->filled('date_from') || $request->filled('date_to'))
            &nbsp;|&nbsp; Period: {{ $request->date_from ?? '—' }} to {{ $request->date_to ?? 'Present' }}
        @endif
        @if($request->filled('status'))
            &nbsp;|&nbsp; Status: {{ ucfirst($request->status) }}
        @endif
    </div>
</div>

<div class="summary">
    <div class="summary-box">
        <div class="val val-primary">{{ $summary['total'] }}</div>
        <div class="lbl">TOTAL</div>
    </div>
    <div class="summary-box">
        <div class="val val-info">{{ $summary['scheduled'] }}</div>
        <div class="lbl">SCHEDULED</div>
    </div>
    <div class="summary-box">
        <div class="val val-success">{{ $summary['completed'] }}</div>
        <div class="lbl">COMPLETED</div>
    </div>
    <div class="summary-box">
        <div class="val val-danger">{{ $summary['cancelled'] }}</div>
        <div class="lbl">CANCELLED</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Meeting No.</th>
            <th>Title</th>
            <th>Type</th>
            <th>Date</th>
            <th>Chairperson</th>
            <th>Attendees</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($meetings as $i => $meeting)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><b>{{ $meeting->meeting_number }}</b></td>
            <td>{{ Str::limit($meeting->title, 50) }}</td>
            <td>{{ strtoupper($meeting->type) }}</td>
            <td>{{ $meeting->date?->format('d M Y') ?? '—' }}</td>
            <td>{{ $meeting->chairperson?->name ?? '—' }}</td>
            <td style="text-align:center">{{ $meeting->attendees_count }}</td>
            <td>
                <span class="badge badge-{{ $meeting->status }}">{{ ucfirst($meeting->status) }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:20px;color:#94a3b8">No meetings found.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <span>BoardMeet &mdash; Confidential</span>
    <span>Total Records: {{ $meetings->count() }}</span>
</div>

</body>
</html>
