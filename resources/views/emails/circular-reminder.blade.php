{{-- resources/views/emails/circular-reminder.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #3b82f6; padding: 24px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px;
                 background: rgba(255,255,255,0.2); color: #fff; margin-top: 6px; }
        .body { padding: 32px; }
        .meta { background: #f8fafc; border-radius: 6px; padding: 16px; margin-bottom: 24px; font-size: 14px; }
        .meta p { margin: 4px 0; color: #64748b; }
        .meta strong { color: #1e293b; }
        .content { font-size: 15px; line-height: 1.7; color: #334155; }
        .footer { padding: 20px 32px; background: #f8fafc; font-size: 12px; color: #94a3b8; text-align: center; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 24px; background: #3b82f6;
               color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BoardMeet</h1>
            <span class="badge">{{ ucfirst($circular->type) }}</span>
        </div>
        <div class="body">
            <p style="font-size:15px; color:#334155; margin-top:0;">
                Dear <strong>{{ $recipientName }}</strong>,
            </p>
            <p style="color:#64748b; font-size:14px;">
                This is a reminder for the following {{ $circular->type }}:
            </p>

            <div class="meta">
                <p><strong>Title:</strong> {{ $circular->title }}</p>
                @if($circular->reference_number)
                    <p><strong>Ref No:</strong> {{ $circular->reference_number }}</p>
                @endif
                <p><strong>Issued Date:</strong> {{ $circular->issued_date->format('d M, Y') }}</p>
                <p><strong>Type:</strong> {{ ucfirst($circular->type) }}</p>
            </div>

            <div class="content">
                {!! nl2br(e($circular->body)) !!}
            </div>

            @if($circular->attachments->isNotEmpty())
                <p style="margin-top:20px; font-size:14px; color:#64748b;">
                    <strong>{{ $circular->attachments->count() }} attachment(s)</strong> available. Please log in to download.
                </p>
            @endif
        </div>
        <div class="footer">
            This email was sent by BoardMeet. Please do not reply to this email.
        </div>
    </div>
</body>
</html>
