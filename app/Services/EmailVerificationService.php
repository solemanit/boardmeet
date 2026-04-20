<?php

namespace App\Services;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailVerificationService
{
    /**
     * FIX #8: Increased from 5 to 60 minutes.
     * 5 minutes is too tight — email delivery delays are common
     * and users would frequently get expired tokens.
     */
    protected int $tokenExpiration = 60;

    /**
     * Generate and send verification token to user
     */
    public function generateAndSendToken(User $user): EmailVerificationToken
    {
        // Invalidate any existing tokens
        EmailVerificationToken::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        $token = EmailVerificationToken::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addMinutes($this->tokenExpiration),
        ]);

        try {
            $token->load('user');

            Log::info('Sending email verification notification', [
                'user_id'  => $user->id,
                'email'    => $user->email,
                'token_id' => $token->id,
            ]);

            $user->notify(new VerifyEmailNotification($token));

            Log::info('Email verification notification sent successfully', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send email verification notification', [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'error'     => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }

        return $token;
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token, string $email): array
    {
        $verificationToken = EmailVerificationToken::where('token', $token)
            ->with('user')
            ->first();

        if (!$verificationToken) {
            return [
                'success' => false,
                'message' => 'Invalid verification token',
            ];
        }

        if ($verificationToken->user->email !== $email) {
            return [
                'success' => false,
                'message' => 'Email does not match',
            ];
        }

        if ($verificationToken->isExpired()) {
            return [
                'success' => false,
                'message' => 'Verification token has expired. Please request a new one.',
                'code'    => 'token_expired',
            ];
        }

        if ($verificationToken->isVerified()) {
            return [
                'success' => false,
                'message' => 'Email already verified',
            ];
        }

        $verificationToken->markAsVerified();
        $verificationToken->user->update([
            'email_verified_at' => now(),
            'is_active'         => true,
            'activated_at'      => now(),
        ]);

        $verificationToken->refresh();
        $verificationToken->load('user');

        Log::info('User account activated via email verification', [
            'user_id' => $verificationToken->user_id,
            'email'   => $verificationToken->user->email,
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully. Your account is now active.',
            'user'    => $verificationToken->user->fresh(),
        ];
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(User $user): array
    {
        if ($user->email_verified_at) {
            Log::info('Resend verification attempt for already verified email', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return [
                'success' => false,
                'message' => 'Email is already verified',
            ];
        }

        try {
            $this->generateAndSendToken($user);

            Log::info('Resend verification email requested', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return [
                'success' => true,
                'message' => 'Verification email sent successfully. Please check your inbox.',
            ];
        } catch (Exception $e) {
            Log::error('Failed to resend verification email', [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'error'     => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send verification email. Please try again later.',
            ];
        }
    }

    /**
     * Check if user's email is verified
     */
    public function isEmailVerified(User $user): bool
    {
        return $user->email_verified_at !== null;
    }

    /**
     * Get the expiration time in minutes
     */
    public function getTokenExpiration(): int
    {
        return $this->tokenExpiration;
    }
}
