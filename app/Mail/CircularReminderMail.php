<?php
// app/Mail/CircularReminderMail.php

namespace App\Mail;

use App\Models\Circular;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CircularReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Circular $circular,
        public readonly string   $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        $prefix = match ($this->circular->type) {
            'notice'   => '[Notice]',
            'memo'     => '[Memo]',
            default    => '[Circular]',
        };

        return new Envelope(
            subject: "{$prefix} {$this->circular->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.circular-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
