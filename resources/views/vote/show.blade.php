<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote — {{ $meeting->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .vote-shell { max-width: 680px; margin: 0 auto; padding: 24px 16px 60px; }

        .vote-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 14px; color: #fff; padding: 28px 28px 24px;
            margin-bottom: 24px;
        }
        .vote-header h1 { font-size: 20px; font-weight: 700; margin: 0 0 6px; }
        .vote-meta { font-size: 13px; opacity: .85; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
        .vote-meta span { display: flex; align-items: center; gap: 5px; }

        .voter-card {
            background: #fff; border-radius: 12px; padding: 22px 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 20px;
            border: 1.5px solid #e2e8f0;
        }
        .voter-card h2 { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #1e293b; }

        .memo-card {
            background: #fff; border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            margin-bottom: 16px; overflow: hidden;
            border: 1.5px solid #e2e8f0;
        }
        .memo-card-header {
            background: #f8fafc; padding: 14px 20px;
            border-bottom: 1.5px solid #e2e8f0;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .memo-num {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 24px; min-width: 24px; border-radius: 50%;
            background: #3b82f6; color: #fff; font-size: 11px; font-weight: 700;
            flex-shrink: 0; margin-top: 1px;
        }
        .memo-title { font-weight: 700; font-size: 14px; color: #1e293b; }
        .memo-desc  { font-size: 13px; color: #64748b; margin-top: 4px; white-space: pre-wrap; line-height: 1.5; }
        .req-pill {
            margin-left: auto; flex-shrink: 0;
            font-size: 10px; font-weight: 700; border-radius: 20px; padding: 3px 10px;
            background: #fffbeb; color: #92400e; border: 1px solid #fde68a;
            text-transform: uppercase;
        }
        .opt-pill {
            margin-left: auto; flex-shrink: 0;
            font-size: 10px; font-weight: 700; border-radius: 20px; padding: 3px 10px;
            background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;
            text-transform: uppercase;
        }

        .vote-options { display: flex; gap: 10px; padding: 16px 20px; flex-wrap: wrap; }
        .vote-opt-label {
            flex: 1; min-width: 80px; display: flex; align-items: center; justify-content: center;
            gap: 6px; padding: 12px 10px; border: 2px solid #e2e8f0;
            border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 600;
            color: #64748b; background: #f8fafc; transition: all .15s; user-select: none;
            text-align: center;
        }
        .vote-opt-label input { display: none; }
        .vote-opt-label:hover { border-color: #cbd5e1; background: #f1f5f9; }
        .vote-opt-label.sel-approve { border-color: #16a34a; background: #f0fdf4; color: #16a34a; }
        .vote-opt-label.sel-reject  { border-color: #dc2626; background: #fef2f2; color: #dc2626; }
        .vote-opt-label.sel-abstain { border-color: #94a3b8; background: #f8fafc; color: #475569; }

        .section-label {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: #94a3b8; margin: 24px 0 10px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-label::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

        .submit-btn {
            width: 100%; padding: 14px; font-size: 15px; font-weight: 700;
            border-radius: 10px; border: none; cursor: pointer;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff; margin-top: 24px;
            transition: opacity .15s, transform .1s;
        }
        .submit-btn:hover { opacity: .92; }
        .submit-btn:active { transform: scale(.99); }
        .submit-btn:disabled { opacity: .6; cursor: not-allowed; }

        .progress-bar-custom {
            height: 4px; background: #e2e8f0; border-radius: 2px;
            margin-top: 12px; overflow: hidden;
        }
        .progress-fill {
            height: 100%; background: #3b82f6; border-radius: 2px;
            transition: width .3s;
        }
        .vote-counter { font-size: 12px; color: #94a3b8; text-align: right; margin-top: 4px; }
    </style>
</head>
<body>
<div class="vote-shell">

    {{-- Header --}}
    <div class="vote-header">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;opacity:.7;margin-bottom:4px;">
            BoardMeet · Voting Portal
        </div>
        <h1>{{ $meeting->title }}</h1>
        <div class="vote-meta">
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                {{ $meeting->date->format('d M Y') }}
            </span>
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7V12L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                {{ $meeting->start_time }}
            </span>
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 5.55228 9.44772 6 10 6H14C14.5523 6 15 5.55228 15 5M9 5C9 4.44772 9.44772 4 10 4H14C14.5523 4 15 4.44772 15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                {{ $votableMemos->count() }} Voting Item{{ $votableMemos->count() !== 1 ? 's' : '' }}
            </span>
            @if($link->expires_at)
            <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M12 9V13L14.5 15.5M20.9999 12C20.9999 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 20.9999 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Closes {{ $link->expires_at->format('d M Y H:i') }}
            </span>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('vote.submit', $link->token) }}" method="POST" id="voteForm">
        @csrf

        {{-- Voter Information --}}
        <div class="voter-card">
            <h2>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:-2px;margin-right:6px;">
                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Your Information
            </h2>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label fw-medium" style="font-size:13px;">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="voter_name" class="form-control @error('voter_name') is-invalid @enderror"
                        value="{{ old('voter_name') }}" placeholder="e.g. John Smith" required>
                    @error('voter_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium" style="font-size:13px;">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="voter_email" class="form-control @error('voter_email') is-invalid @enderror"
                        value="{{ old('voter_email') }}" placeholder="e.g. john@company.com" required>
                    @error('voter_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <p class="mb-0 mt-3" style="font-size:11px;color:#94a3b8;">
                Your email prevents duplicate submissions. It will not be publicly displayed.
            </p>
        </div>

        {{-- Progress tracker --}}
        <div style="background:#fff;border-radius:10px;padding:14px 18px;border:1.5px solid #e2e8f0;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:600;color:#1e293b;">Voting Progress</span>
                <span class="vote-counter" id="voteCounter">0 / {{ $votableMemos->count() }} voted</span>
            </div>
            <div class="progress-bar-custom">
                <div class="progress-fill" id="progressFill" style="width:0%"></div>
            </div>
        </div>

        {{-- Memos grouped by section --}}
        @foreach($meeting->agendaSections as $si => $section)
            @php $sectionVotable = $section->memos->where('vote_required', '!=', 'none'); @endphp
            @if($sectionVotable->isNotEmpty())
                <div class="section-label">
                    <span>{{ $si + 1 }}. {{ $section->title }}</span>
                </div>

                @foreach($sectionVotable as $mi => $memo)
                <div class="memo-card">
                    <div class="memo-card-header">
                        <span class="memo-num">{{ $mi + 1 }}</span>
                        <div class="flex-grow-1">
                            <div class="memo-title">{{ $memo->title }}</div>
                            @if($memo->description)
                                <div class="memo-desc">{{ $memo->description }}</div>
                            @endif
                        </div>
                        @if($memo->vote_required === 'required')
                            <span class="req-pill">Required</span>
                        @else
                            <span class="opt-pill">Optional</span>
                        @endif
                    </div>
                    <div class="vote-options" id="opts-{{ $memo->id }}">
                        <label class="vote-opt-label" data-val="approve" data-memo="{{ $memo->id }}">
                            <input type="radio" name="votes[{{ $memo->id }}]" value="approve"
                                {{ $memo->vote_required === 'required' ? 'required' : '' }}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                            Approve
                        </label>
                        <label class="vote-opt-label" data-val="reject" data-memo="{{ $memo->id }}">
                            <input type="radio" name="votes[{{ $memo->id }}]" value="reject"
                                {{ $memo->vote_required === 'required' ? 'required' : '' }}>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                            Reject
                        </label>
                        <label class="vote-opt-label" data-val="abstain" data-memo="{{ $memo->id }}">
                            <input type="radio" name="votes[{{ $memo->id }}]" value="abstain">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                            Abstain
                        </label>
                    </div>
                </div>
                @endforeach
            @endif
        @endforeach

        <button type="submit" class="submit-btn" id="submitBtn" disabled>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:-2px;margin-right:6px;">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Submit My Votes
        </button>
    </form>
</div>

<script>
const TOTAL      = {{ $votableMemos->count() }};
const voted      = {};

// Radio visual selection
document.querySelectorAll('.vote-opt-label').forEach(label => {
    label.addEventListener('click', () => {
        const memoId = label.dataset.memo;
        const val    = label.dataset.val;

        // Clear siblings
        document.querySelectorAll(`.vote-opt-label[data-memo="${memoId}"]`).forEach(l => {
            l.classList.remove('sel-approve', 'sel-reject', 'sel-abstain');
        });
        label.classList.add('sel-' + val);
        label.querySelector('input').checked = true;

        voted[memoId] = val;
        updateProgress();
    });
});

function updateProgress() {
    const count = Object.keys(voted).length;
    const pct   = TOTAL > 0 ? Math.round(count / TOTAL * 100) : 0;
    document.getElementById('voteCounter').textContent  = count + ' / ' + TOTAL + ' voted';
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('submitBtn').disabled        = count === 0;
}

// Confirm before submit
document.getElementById('voteForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting…';
});
</script>
</body>
</html>
