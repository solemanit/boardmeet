<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You — BoardMeet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .thanks-shell { max-width: 480px; margin: 80px auto; padding: 0 16px; text-align: center; }
        .thanks-icon  {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(22,163,74,.3);
        }
        .thanks-card  {
            background: #fff; border-radius: 16px; padding: 40px 32px;
            box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1.5px solid #e2e8f0;
        }
        h1 { font-size: 24px; font-weight: 800; color: #1e293b; margin-bottom: 10px; }
        p  { color: #64748b; font-size: 14px; line-height: 1.7; }
    </style>
</head>
<body>
<div class="thanks-shell">
    <div class="thanks-card">
        <div class="thanks-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                <path d="M20 6L9 17L4 12" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Vote Submitted!</h1>
        @if(session('voter_name'))
            <p>Thank you, <strong>{{ session('voter_name') }}</strong>! Your votes have been recorded successfully.</p>
        @else
            <p>Your votes have been recorded successfully.</p>
        @endif
        <p style="font-size:13px;">
            <strong>Meeting:</strong> {{ $meeting->title }}<br>
            <strong>Date:</strong> {{ $meeting->date->format('d M Y') }}
        </p>
        <hr style="border-color:#f1f5f9;margin:20px 0;">
        <p style="font-size:12px;color:#94a3b8;">You may now close this window. Each person can only vote once per meeting.</p>
    </div>
</div>
</body>
</html>
