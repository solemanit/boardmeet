<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingNotification extends Notification
{
    public function __construct(
        private Meeting $meeting,
        private string  $type = 'invite'   // invite | reschedule | cancel | reminder
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->type) {
            'invite'     => "Meeting Invitation: {$this->meeting->title}",
            'reschedule' => "Meeting Rescheduled: {$this->meeting->title}",
            'cancel'     => "Meeting Cancelled: {$this->meeting->title}",
            'reminder'   => "Reminder: {$this->meeting->title} Tomorrow",
            default      => "Meeting Update: {$this->meeting->title}",
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        match ($this->type) {
            'invite' => $mail
                ->line("You are invited to attend the following meeting.")
                ->line("**Meeting No:** {$this->meeting->meeting_number}")
                ->line("**Date:** " . $this->meeting->date->format('d M Y'))
                ->line("**Time:** " . $this->meeting->start_time)
                ->line("**Venue:** " . ($this->meeting->venue ?? ($this->meeting->venue_type === 'virtual' ? 'Online' : 'TBA')))
                ->when(
                    $this->meeting->meeting_link,
                    fn($m) => $m->action('Join via Zoom / Meeting Link', $this->meeting->meeting_link)
                ),

            'cancel' => $mail
                ->line("The following meeting has been **cancelled**.")
                ->line("**Reason:** " . ($this->meeting->cancellation_reason ?? 'N/A')),
            'reschedule' => $mail
                ->line("The meeting has been rescheduled to a new date.")
                ->line("**New Date:** " . $this->meeting->date->format('d M Y'))
                ->line("**New Time:** " . $this->meeting->start_time)
                ->when(
                    $this->meeting->meeting_link,
                    fn($m) => $m->action('Join via Zoom / Meeting Link', $this->meeting->meeting_link)
                ),
            default => $mail->line("Please check the meeting details.")
        };

        return $mail
            ->action('View Meeting', route('meetings.show', $this->meeting))
            ->line("Thank you.");
    }
}
