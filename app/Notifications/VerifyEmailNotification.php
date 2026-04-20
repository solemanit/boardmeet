<?php

namespace App\Notifications;

use App\Models\EmailVerificationToken;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    private EmailVerificationToken $token;

    public function __construct(EmailVerificationToken $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->getVerificationUrl();

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Please verify your email address by clicking the button below.')
            ->line('This verification link will expire in 60 minutes.')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->line('Thank you for registering with us!');
    }

    protected function getVerificationUrl(): string
    {
        return url('/verify-email')
            . '?token=' . $this->token->token
            . '&email=' . urlencode($this->token->user->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token_id'   => $this->token->id,
            'expires_at' => $this->token->expires_at,
        ];
    }
}
