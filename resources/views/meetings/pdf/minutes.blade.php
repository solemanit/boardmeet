{{-- views/layouts/meetings/pdf/minutes.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minutes — {{ $meeting->meeting_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1a1a2e; background: #fff; padding: 40px; }

  @media print {
    body { padding: 20px; }
    .no-print { display: none !important; }
    @page { margin: 15mm 15mm 20mm; size: A4; }
  }

  .no-print {
    position: fixed; top: 0; left: 0; right: 0; z-index: 999;
    background: #1e293b; padding: 10px 20px;
    display: flex; gap: 10px; align-items: center;
  }
  .no-print a, .no-print button {
    padding: 6px 14px; border-radius: 6px; font-size: 12px;
    cursor: pointer; text-decoration: none; border: none;
  }
  .btn-pdf  { background: #3b82f6; color: #fff; }
  .btn-print{ background: #10b981; color: #fff; }
  .btn-back { background: #475569; color: #fff; }
  .no-print .title { color: #94a3b8; font-size: 13px; flex: 1; }

  .print-wrapper { max-width: 750px; margin: 0 auto; }
  @media screen { .print-wrapper { margin-top: 56px; } }

  .doc-header { border-bottom: 3px solid #1e293b; padding-bottom: 16px; margin-bottom: 24px; }
  .doc-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; }
  .doc-header .sub { font-size: 13px; color: #64748b; margin-top: 4px; }
  .meta-grid  { display: table; width: 100%; margin-top: 16px; border-collapse: collapse; }
  .meta-row   { display: table-row; }
  .meta-label { display: table-cell; width: 140px; font-weight: 600; color: #475569; padding: 4px 0; font-size: 11px; text-transform: uppercase; letter-spacing:.5px; }
  .meta-value { display: table-cell; color: #1e293b; padding: 4px 0; font-size: 12px; }

  .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: #475569; margin: 28px 0 12px;
    padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

  /* Agenda + minutes block */
  .agenda-block { margin-bottom: 20px; border: 1px solid #e2e8f0; border-radius: 6px; page-break-inside: avoid; }
  .agenda-block-header { display: flex; gap: 12px; align-items: flex-start;
    background: #f8fafc; padding: 10px 14px; border-bottom: 1px solid #e2e8f0;
    border-radius: 6px 6px 0 0; }
  .agenda-num { min-width: 26px; height: 26px; width: 26px; border-radius: 50%;
    background: #1e293b; color: #fff; display: flex; align-items: center;
    justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
  .agenda-title  { font-weight: 700; font-size: 12px; color: #1e293b; }
  .agenda-details{ font-size: 11px; color: #64748b; margin-top: 3px; white-space: pre-wrap; }
  .minutes-box   { padding: 12px 14px; }
  .minutes-label { font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #64748b; margin-bottom: 6px; }
  .minutes-text  { font-size: 12px; color: #1e293b; white-space: pre-wrap; line-height: 1.6; }
  .no-minutes    { font-size: 11px; color: #94a3b8; font-style: italic; }

  /* Attendance table */
  table.att { width: 100%; border-collapse: collapse; margin-top: 8px; }
  table.att th { background: #f1f5f9; padding: 7px 10px; text-align: left;
    font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #475569; }
  table.att td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
  .att-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
  .att-attended  { background: #dcfce7; color: #166534; }
  .att-confirmed { background: #dbeafe; color: #1e40af; }
  .att-absent    { background: #f1f5f9; color: #475569; }
  .att-declined  { background: #fee2e2; color: #991b1b; }
  .att-invited   { background: #fef9c3; color: #854d0e; }

  /* Signature section */
  .sig-row { display: flex; gap: 40px; margin-top: 50px; }
  .sig-box { flex: 1; border-top: 1px solid #1e293b; padding-top: 8px; }
  .sig-name{ font-size: 11px; font-weight: 600; color: #1e293b; }
  .sig-role{ font-size: 10px; color: #64748b; }

  .doc-footer { margin-top: 30px; padding-top: 14px; border-top: 1px solid #e2e8f0;
    display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; }

  .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
  .badge-scheduled { background:#dbeafe; color:#1e40af; }
  .badge-completed  { background:#dcfce7; color:#166534; }
  .badge-cancelled  { background:#fee2e2; color:#991b1b; }
  .badge-ongoing    { background:#fef9c3; color:#854d0e; }
  .badge-postponed  { background:#f1f5f9; color:#475569; }
</style>
</head>
<body>

<div class="no-print">
  <span class="title">{{ $meeting->meeting_number }} — Minutes Preview</span>
  <a href="{{ route('meetings.export.minutes', $meeting) }}" class="btn-pdf">⬇ Download PDF</a>
  <button onclick="window.print()" class="btn-print">🖨 Print</button>
  <a href="{{ route('meetings.show', $meeting) }}" class="btn-back">← Back</a>
</div>

<div class="print-wrapper">

  <div class="doc-header">
    <h1>Meeting Minutes</h1>
    <div class="sub">{{ $meeting->title }}</div>

    <div class="meta-grid">
      <div class="meta-row">
        <span class="meta-label">Meeting No.</span>
        <span class="meta-value"><strong>{{ $meeting->meeting_number }}</strong></span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Type</span>
        <span class="meta-value">{{ strtoupper($meeting->type) }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Date</span>
        <span class="meta-value">{{ $meeting->date->format('d F Y') }}</span>
      </div>
      <div class="meta-row">
        <span class="meta-label">Time</span>
        <span class="meta-value">
          {{ $meeting->start_time }}{{ $meeting->end_time ? ' – ' . $meeting->end_time : '' }}
        </span>
      </div>
      @if($meeting->venue)
      <div class="meta-row">
        <span class="meta-label">Venue</span>
        <span class="meta-value">{{ $meeting->venue }}</span>
      </div>
      @endif
      @if($meeting->chairperson)
      <div class="meta-row">
        <span class="meta-label">Chairperson</span>
        <span class="meta-value">{{ $meeting->chairperson->name }}</span>
      </div>
      @endif
      @if($meeting->secretary)
      <div class="meta-row">
        <span class="meta-label">Secretary</span>
        <span class="meta-value">{{ $meeting->secretary->name }}</span>
      </div>
      @endif
      <div class="meta-row">
        <span class="meta-label">Status</span>
        <span class="meta-value">
          <span class="badge badge-{{ $meeting->status }}">{{ ucfirst($meeting->status) }}</span>
        </span>
      </div>
    </div>
  </div>

{{-- Agenda + Minutes --}}
  <div class="section-title">Minutes of Discussion</div>

  @forelse($meeting->agendaSections as $si => $section)

    {{-- Section heading --}}
    <div style="margin: 24px 0 10px; padding: 7px 12px; background: #f1f5f9;
                border-left: 3px solid #1e293b; border-radius: 3px; page-break-after: avoid;">
      <span style="font-size:12px; font-weight:700; color:#1e293b;">
        {{ $si + 1 }}. {{ $section->title }}
      </span>
    </div>

    @forelse($section->memos as $mi => $memo)
      <div class="agenda-block" style="margin-left: 12px;">
        <div class="agenda-block-header">
          <div class="agenda-num" style="background:#475569;">{{ $mi + 1 }}</div>
          <div style="flex:1;">
            <div class="agenda-title">{{ $memo->title }}</div>
            @if($memo->description)
              <div class="agenda-details">{{ $memo->description }}</div>
            @endif

            {{-- Vote result inline --}}
            @if($memo->vote_required !== 'none')
              @php $tally = $memo->voteTally(); $outcome = $memo->voteOutcome(); @endphp
              <div style="margin-top:5px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span style="font-size:10px; color:#64748b;">
                  Vote: {{ ucfirst($memo->vote_required) }}
                </span>
                @if($tally['total'] > 0)
                  <span style="font-size:10px; color:#166534;">✓ {{ $tally['approve'] }}</span>
                  <span style="font-size:10px; color:#991b1b;">✕ {{ $tally['reject'] }}</span>
                  <span style="font-size:10px; color:#475569;">— {{ $tally['abstain'] }}</span>
                  <span style="display:inline-block; padding:1px 7px; border-radius:3px;
                               font-size:10px; font-weight:600;
                               background:{{ match($outcome) {
                                   'passed'   => '#dcfce7',
                                   'rejected' => '#fee2e2',
                                   'tied'     => '#fef9c3',
                                   default    => '#f1f5f9',
                               } }};
                               color:{{ match($outcome) {
                                   'passed'   => '#166534',
                                   'rejected' => '#991b1b',
                                   'tied'     => '#854d0e',
                                   default    => '#475569',
                               } }};">
                    {{ ucfirst($outcome) }}
                  </span>
                @else
                  <span style="font-size:10px; color:#94a3b8; font-style:italic;">No votes cast</span>
                @endif
              </div>
            @endif
          </div>
        </div>

        <div class="minutes-box">
          <div class="minutes-label">Minutes / Notes</div>
          @if($memo->minutes_note)
            <div class="minutes-text">{{ $memo->minutes_note }}</div>
          @else
            <div class="no-minutes">No minutes recorded for this item.</div>
          @endif
        </div>
      </div>
    @empty
      <p style="margin-left:24px; color:#94a3b8; font-style:italic; font-size:11px;
                margin-bottom:12px;">
        No memos in this section.
      </p>
    @endforelse

  @empty
    <p style="color:#94a3b8; font-style:italic;">No agenda items defined.</p>
  @endforelse

  {{-- Attendance --}}
  @if($meeting->attendees->isNotEmpty())
  <div class="section-title">Attendance</div>
  <table class="att">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Role</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($meeting->attendees as $idx => $att)
      <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $att->user?->name ?? '—' }}</td>
        <td>{{ $att->user?->role?->name ?? '—' }}</td>
        <td>
          <span class="att-badge att-{{ $att->status }}">{{ ucfirst($att->status) }}</span>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- Signature lines --}}
  <div class="sig-row">
    <div class="sig-box">
      <div class="sig-name">{{ $meeting->chairperson?->name ?? '_____________________' }}</div>
      <div class="sig-role">Chairperson</div>
    </div>
    <div class="sig-box">
      <div class="sig-name">{{ $meeting->secretary?->name ?? '_____________________' }}</div>
      <div class="sig-role">Secretary</div>
    </div>
    <div class="sig-box">
      <div class="sig-name">_____________________</div>
      <div class="sig-role">Date</div>
    </div>
  </div>

  <div class="doc-footer">
    <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
    <span>{{ $meeting->meeting_number }} — Confidential</span>
  </div>

</div>
</body>
</html>
