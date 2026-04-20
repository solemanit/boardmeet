<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
    .header { background: #1e40af; color: #fff; padding: 20px 24px; margin-bottom: 20px; }
    .header h1 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
    .header p  { font-size: 10px; opacity: .85; }
    .meta-row { display: flex; gap: 20px; font-size: 10px; margin-top: 6px; }

    .summary { display: flex; gap: 12px; margin-bottom: 18px; }
    .sum-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; }
    .sum-num  { font-size: 18px; font-weight: 800; }
    .sum-lbl  { font-size: 9px; color: #64748b; margin-top: 2px; }

    .section-title { font-size: 12px; font-weight: 700; margin: 16px 0 8px; padding-bottom: 4px; border-bottom: 2px solid #e2e8f0; }

    .memo-block { border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 12px; overflow: hidden; }
    .memo-head  { background: #f8fafc; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
    .memo-title { font-weight: 700; font-size: 12px; }
    .outcome-badge { display: inline-block; font-size: 9px; font-weight: 700; border-radius: 10px; padding: 2px 8px; }
    .oc-passed   { background: #dcfce7; color: #166534; }
    .oc-rejected { background: #fee2e2; color: #991b1b; }
    .oc-tied     { background: #fef9c3; color: #854d0e; }
    .oc-pending  { background: #f1f5f9; color: #64748b; }

    .vote-bar-wrap { padding: 10px 12px 6px; }
    .vote-bar { height: 7px; border-radius: 4px; overflow: hidden; background: #f1f5f9; margin-bottom: 6px; }
    .bar-a { float: left; height: 100%; background: #16a34a; }
    .bar-r { float: left; height: 100%; background: #dc2626; }
    .bar-x { float: left; height: 100%; background: #94a3b8; }

    .tally-row { font-size: 10px; color: #64748b; }
    .tally-row span { margin-right: 14px; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th    { font-size: 9px; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; font-weight: 700; padding: 5px 8px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    td    { font-size: 10px; padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
    .vc   { font-size: 9px; font-weight: 700; border-radius: 10px; padding: 2px 7px; display: inline-block; }
    .vc-a { background: #dcfce7; color: #166534; }
    .vc-r { background: #fee2e2; color: #991b1b; }
    .vc-x { background: #f1f5f9; color: #475569; }

    .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <h1>Vote Report — {{ $meeting->meeting_number }}</h1>
    <p>{{ $meeting->title }}</p>
    <div class="meta-row">
        <span>Date: {{ $meeting->date->format('d M Y') }}</span>
        <span>Generated: {{ now()->format('d M Y H:i') }}</span>
        @if($meeting->chairperson)<span>Chair: {{ $meeting->chairperson->name }}</span>@endif
    </div>
</div>

{{-- Summary --}}
<div class="summary">
    <div class="sum-box">
        <div class="sum-num">{{ $reportData['total_votable'] }}</div>
        <div class="sum-lbl">Voting Items</div>
    </div>
    <div class="sum-box">
        <div class="sum-num" style="color:#16a34a;">{{ $reportData['total_passed'] }}</div>
        <div class="sum-lbl">Passed</div>
    </div>
    <div class="sum-box">
        <div class="sum-num" style="color:#dc2626;">{{ $reportData['total_rejected'] }}</div>
        <div class="sum-lbl">Rejected</div>
    </div>
    <div class="sum-box">
        <div class="sum-num">{{ $reportData['total_votes_cast'] }}</div>
        <div class="sum-lbl">Votes Cast</div>
    </div>
</div>

{{-- Sections & memos --}}
@foreach($reportData['sections'] as $sData)
<div class="section-title">{{ $loop->iteration }}. {{ $sData['section']->title }}</div>

@foreach($sData['memos'] as $mData)
@php $m = $mData['memo']; $t = $mData['tally']; @endphp
<div class="memo-block">
    <div class="memo-head" style="display:flex;justify-content:space-between;align-items:center;">
        <span class="memo-title">{{ $m->title }}</span>
        <span class="outcome-badge oc-{{ $mData['outcome'] }}">{{ ucfirst($mData['outcome']) }}</span>
    </div>
    <div class="vote-bar-wrap">
        @if($t['total'] > 0)
        <div class="vote-bar" style="overflow:hidden;">
            <div class="bar-a" style="width:{{ $mData['pct_approve'] }}%;"></div>
            <div class="bar-r" style="width:{{ $mData['pct_reject'] }}%;"></div>
            <div class="bar-x" style="width:{{ $mData['pct_abstain'] }}%;"></div>
        </div>
        <div class="tally-row" style="clear:both;">
            <span>✓ {{ $t['approve'] }} Approve ({{ $mData['pct_approve'] }}%)</span>
            <span>✕ {{ $t['reject'] }} Reject ({{ $mData['pct_reject'] }}%)</span>
            <span>— {{ $t['abstain'] }} Abstain ({{ $mData['pct_abstain'] }}%)</span>
            <span>Total: {{ $t['total'] }}</span>
        </div>
        @else
        <p style="color:#94a3b8;font-size:10px;font-style:italic;">No votes yet.</p>
        @endif

        @if($mData['all_voters']->isNotEmpty())
        <table>
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Type</th><th>Vote</th><th>Time</th></tr>
            </thead>
            <tbody>
                @foreach($mData['all_voters'] as $vi => $voter)
                <tr>
                    <td style="color:#94a3b8;">{{ $vi+1 }}</td>
                    <td><strong>{{ $voter['name'] }}</strong></td>
                    <td>{{ $voter['email'] }}</td>
                    <td>{{ ucfirst($voter['type']) }}</td>
                    <td>
                        <span class="vc vc-{{ $voter['vote'][0] }}">
                            {{ match($voter['vote']) { 'approve'=>'✓ Approve','reject'=>'✕ Reject',default=>'— Abstain' } }}
                        </span>
                    </td>
                    <td style="color:#94a3b8;">{{ $voter['voted_at'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endforeach
@endforeach

<div class="footer">BoardMeet · {{ $meeting->meeting_number }} Vote Report · Generated {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
