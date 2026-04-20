{{-- views/layouts/meetings/pdf/agenda.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agenda — {{ $meeting->meeting_number }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1a1a2e; background: #fff; padding: 40px; }

  /* Print overrides */
  @media print {
    body { padding: 20px; }
    .no-print { display: none !important; }
    @page { margin: 15mm 15mm 20mm; size: A4; }
  }

  /* No-print toolbar */
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

  /* Header */
  .doc-header { border-bottom: 3px solid #1e293b; padding-bottom: 16px; margin-bottom: 24px; }
  .doc-header h1 { font-size: 20px; font-weight: 700; color: #1e293b; }
  .doc-header .sub { font-size: 13px; color: #64748b; margin-top: 4px; }
  .meta-grid { display: table; width: 100%; margin-top: 16px; border-collapse: collapse; }
  .meta-row  { display: table-row; }
  .meta-label{ display: table-cell; width: 140px; font-weight: 600; color: #475569; padding: 4px 0; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
  .meta-value{ display: table-cell; color: #1e293b; padding: 4px 0; font-size: 12px; }

  /* Section */
  .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .8px; color: #475569; margin: 28px 0 12px;
    padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

  /* Agenda items */
  .agenda-item { display: flex; gap: 14px; margin-bottom: 14px; padding: 12px 14px;
    border: 1px solid #e2e8f0; border-radius: 6px; page-break-inside: avoid; }
  .agenda-num  { min-width: 28px; height: 28px; width: 28px; border-radius: 50%;
    background: #1e293b; color: #fff; display: flex; align-items: center;
    justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
  .agenda-body { flex: 1; }
  .agenda-body .atitle { font-weight: 700; font-size: 12px; color: #1e293b; }
  .agenda-body .adetails { font-size: 11px; color: #64748b; margin-top: 4px; white-space: pre-wrap; }

  /* Footer */
  .doc-footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #e2e8f0;
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

{{-- Toolbar (screen only) --}}
<div class="no-print">
  <span class="title">{{ $meeting->meeting_number }} — Agenda Preview</span>
  <a href="{{ route('meetings.export.agenda', $meeting) }}" class="btn-pdf">⬇ Download PDF</a>
  <button onclick="window.print()" class="btn-print">🖨 Print</button>
  <a href="{{ route('meetings.show', $meeting) }}" class="btn-back">← Back</a>
</div>

<div class="print-wrapper">

  {{-- Document header --}}
  <div class="doc-header">
    <h1>Meeting Agenda</h1>
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
      @elseif($meeting->meeting_link)
      <div class="meta-row">
        <span class="meta-label">Link</span>
        <span class="meta-value">{{ $meeting->meeting_link }}</span>
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

{{-- Agenda items --}}
  @php $totalMemos = $meeting->agendaSections->sum(fn($s) => $s->memos->count()); @endphp
  <div class="section-title">Agenda Items ({{ $totalMemos }})</div>

  @forelse($meeting->agendaSections as $si => $section)

    {{-- Section heading --}}
    <div style="margin: 20px 0 8px; padding: 6px 10px; background: #f1f5f9;
                border-left: 3px solid #1e293b; border-radius: 3px;">
      <span style="font-size:11px; font-weight:700; text-transform:uppercase;
                   letter-spacing:.6px; color:#475569;">
        {{ $si + 1 }}. {{ $section->title }}
      </span>
    </div>

    @forelse($section->memos as $mi => $memo)
      <div class="agenda-item" style="margin-left: 12px;">
        <div class="agenda-num" style="background:#475569;">{{ $mi + 1 }}</div>
        <div class="agenda-body">
          <div class="atitle">{{ $memo->title }}</div>
          @if($memo->description)
            <div class="adetails">{{ $memo->description }}</div>
          @endif
          @if($memo->vote_required !== 'none')
            <div style="margin-top:4px;">
              <span style="display:inline-block; padding:1px 7px; border-radius:3px; font-size:10px;
                           font-weight:600;
                           background:{{ $memo->vote_required === 'required' ? '#fef9c3' : '#dbeafe' }};
                           color:{{ $memo->vote_required === 'required' ? '#854d0e' : '#1e40af' }};">
                Vote: {{ ucfirst($memo->vote_required) }}
              </span>
            </div>
          @endif
          @if($memo->documents->isNotEmpty())
            <div style="margin-top:4px; font-size:10px; color:#64748b;">
              📎 {{ $memo->documents->count() }} attachment{{ $memo->documents->count() !== 1 ? 's' : '' }}
            </div>
          @endif
        </div>
      </div>
    @empty
      <p style="margin-left:24px; color:#94a3b8; font-style:italic; font-size:11px;">
        No memos in this section.
      </p>
    @endforelse

  @empty
    <p style="color:#94a3b8; font-style:italic;">No agenda items defined.</p>
  @endforelse

  {{-- Footer --}}
  <div class="doc-footer">
    <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
    <span>{{ $meeting->meeting_number }} — Confidential</span>
  </div>

</div>
</body>
</html>
