<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1a1a2e; line-height: 1.6; }

    .letterhead { text-align: center; padding: 20px 30px 14px; border-bottom: 3px double #1a1a2e; margin-bottom: 18px; }
    .letterhead h1 { font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .letterhead h2 { font-size: 13px; font-weight: 600; margin-top: 4px; color: #3b82f6; }
    .letterhead .ref { font-size: 9px; color: #718096; margin-top: 6px; }

    .meta-grid { display: table; width: 100%; margin: 0 30px 18px; width: calc(100% - 60px); border: 1px solid #e2e8f0; border-radius: 4px; }
    .meta-row { display: table-row; }
    .meta-cell { display: table-cell; padding: 5px 10px; font-size: 9.5px; border-bottom: 1px solid #f1f5f9; }
    .meta-cell.label { font-weight: 700; width: 140px; color: #475569; background: #f8fafc; }

    .section { margin: 0 30px 16px; }
    .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;
        color: #fff; background: #1a1a2e; padding: 5px 10px; margin-bottom: 8px; border-radius: 3px; }

    /* Attendees */
    .attendee-list { display: flex; flex-wrap: wrap; gap: 6px; }
    .attendee-chip { border: 1px solid #e2e8f0; border-radius: 3px; padding: 3px 8px; font-size: 9px; background: #f8fafc; }
    .attendee-chip.present { border-color: #10b981; }
    .attendee-chip.absent  { border-color: #ef4444; opacity: 0.65; }

    /* Agenda Items */
    .agenda-item { border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 10px; overflow: hidden; }
    .agenda-item-header { background: #f1f5f9; padding: 6px 10px; display: flex; gap: 8px; align-items: center; }
    .agenda-item-num  { font-weight: 700; font-size: 10px; color: #3b82f6; min-width: 22px; }
    .agenda-item-title { font-weight: 700; font-size: 10px; flex: 1; }
    .agenda-item-dur  { font-size: 8.5px; color: #94a3b8; }
    .agenda-item-body { padding: 8px 10px; font-size: 9.5px; color: #374151; }
    .agenda-item-body .notes-label { font-weight: 700; font-size: 8.5px; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }

    /* Signature block */
    .sig-block { display: table; width: calc(100% - 60px); margin: 20px 30px 0; border-top: 1px solid #e2e8f0; padding-top: 14px; }
    .sig-cell { display: table-cell; width: 50%; font-size: 9px; }
    .sig-line { border-top: 1px solid #1a1a2e; width: 160px; margin-top: 30px; margin-bottom: 4px; }

    .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 5px 30px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
</style>
</head>
<body>

<div class="letterhead">
    <h1>Minutes of Meeting</h1>
    <h2>{{ $meeting->title }}</h2>
    <div class="ref">Ref: {{ $meeting->meeting_number }} &nbsp;&bull;&nbsp; {{ $meeting->type ? strtoupper($meeting->type) . ' MEETING' : '' }}</div>
</div>

{{-- Meeting Details --}}
<div class="meta-grid">
    <div class="meta-row">
        <div class="meta-cell label">Date</div>
        <div class="meta-cell">{{ $meeting->date?->format('l, d F Y') ?? '—' }}</div>
        <div class="meta-cell label">Time</div>
        <div class="meta-cell">{{ $meeting->start_time ?? '—' }} – {{ $meeting->end_time ?? '—' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-cell label">Venue</div>
        <div class="meta-cell">{{ $meeting->venue ?? '—' }}{{ $meeting->venue_type === 'online' ? ' (Online)' : '' }}</div>
        <div class="meta-cell label">Meeting Link</div>
        <div class="meta-cell">{{ $meeting->meeting_link ?? 'N/A' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-cell label">Chairperson</div>
        <div class="meta-cell">{{ $meeting->chairperson?->name ?? '—' }}</div>
        <div class="meta-cell label">Secretary</div>
        <div class="meta-cell">{{ $meeting->secretary?->name ?? '—' }}</div>
    </div>
</div>

{{-- Attendees --}}
@php
    $presentAttendees = $meeting->attendees->where('status', 'present');
    $absentAttendees  = $meeting->attendees->where('status', 'absent');
@endphp
<div class="section">
    <div class="section-title">Attendees ({{ $presentAttendees->count() }} Present, {{ $absentAttendees->count() }} Absent)</div>
    <div class="attendee-list">
        @foreach($presentAttendees as $att)
            <span class="attendee-chip present">✓ {{ $att->user?->name ?? '—' }}</span>
        @endforeach
        @foreach($absentAttendees as $att)
            <span class="attendee-chip absent">✗ {{ $att->user?->name ?? '—' }}</span>
        @endforeach
    </div>
</div>

{{-- Agenda Items / Minutes --}}
<div class="section">
    <div class="section-title">Agenda Items & Minutes</div>

    @forelse($agendaItems as $item)
    <div class="agenda-item">
        <div class="agenda-item-header">
            <span class="agenda-item-num">{{ $item->order ?? $loop->iteration }}.</span>
            <span class="agenda-item-title">{{ $item->title }}</span>
            @if($item->duration_minutes)
            <span class="agenda-item-dur">{{ $item->duration_minutes }} min</span>
            @endif
        </div>
        <div class="agenda-item-body">
            @if($item->description)
            <div style="margin-bottom:6px">{{ $item->description }}</div>
            @endif

            @if($item->notes)
            <div class="notes-label">Discussion / Minutes</div>
            <div>{{ $item->notes }}</div>
            @endif

            @if($item->action_required)
            <div style="margin-top:6px;padding:5px 8px;background:#fefce8;border-left:3px solid #eab308;font-size:9px">
                <b>Action Required:</b> {{ $item->action_required }}
                @if($item->action_owner) &nbsp;— <i>Owner: {{ $item->action_owner }}</i> @endif
                @if($item->action_deadline) &nbsp;| <i>Due: {{ \Carbon\Carbon::parse($item->action_deadline)->format('d M Y') }}</i> @endif
            </div>
            @endif
        </div>
    </div>
    @empty
    <p style="color:#94a3b8;font-size:9.5px;padding:8px 0">No agenda items recorded.</p>
    @endforelse
</div>

{{-- Signature --}}
<div class="sig-block">
    <div class="sig-cell">
        <div class="sig-line"></div>
        <b>{{ $meeting->chairperson?->name ?? '___________________' }}</b><br>
        Chairperson
    </div>
    <div class="sig-cell">
        <div class="sig-line"></div>
        <b>{{ $meeting->secretary?->name ?? '___________________' }}</b><br>
        Secretary
    </div>
</div>

<div class="footer">
    <span>BoardMeet &mdash; Official Minutes &mdash; Confidential</span>
    <span>{{ $meeting->meeting_number }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</span>
</div>

</body>
</html>
