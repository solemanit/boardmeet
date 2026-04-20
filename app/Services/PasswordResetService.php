<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class PasswordResetService
{
    /**
     * Password reset token expiration time in minutes
     */
    protected int $tokenExpiration = 60;

    /**
     * Send password reset link to user
     */
    public function sendResetLink(string $email): array
    {
        $user = User::where('email', $email)->first();

        // FIX #1 (part A): When user doesn't exist, return null user so the controller
        // can safely check — never crash on $result['user'].
        if (!$user) {
            Log::info('Password reset requested for non-existent email', ['email' => $email]);
            return [
                'success' => false,
                'message' => 'Email not found. Please check and try again.',
                'user'    => null,
            ];
        }

        try {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            $token = Str::random(64);

            // FIX #5: Store expires_at explicitly in DB instead of computing it
            // from created_at at reset time. Cleaner and less error-prone.
            DB::table('password_reset_tokens')->insert([
                'email'      => $email,
                'token'      => Hash::make($token),
                'created_at' => now(),
                'expires_at' => now()->addMinutes($this->tokenExpiration),
            ]);

            Log::info('Password reset token created', [
                'user_id' => $user->id,
                'email'   => $email,
            ]);

            $user->notify(new PasswordResetNotification($token, $email));

            Log::info('Password reset notification sent', [
                'user_id' => $user->id,
                'email'   => $email,
            ]);

            return [
                'success' => true,
                'message' => 'Password reset link has been sent to your email. Please check your inbox.',
                'user'    => $user,
            ];
        } catch (Exception $e) {
            Log::error('Failed to send password reset link', [
                'email'     => $email,
                'error'     => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send password reset link. Please try again later.',
                'user'    => null,
            ];
        }
    }

    /**
     * Verify reset token and reset password
     */
    public function resetPassword(string $token, string $email, string $password): array
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            Log::warning('Password reset attempted with invalid token', ['email' => $email]);
            return [
                'success' => false,
                'message' => 'Invalid or expired password reset link.',
            ];
        }

        if (!Hash::check($token, $resetRecord->token)) {
            Log::warning('Password reset attempted with incorrect token', ['email' => $email]);
            return [
                'success' => false,
                'message' => 'Invalid or expired password reset link.',
            ];
        }

        // Use stored expires_at column (added via migration)
        $isExpired = now()->isAfter($resetRecord->expires_at);

        if ($isExpired) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            Log::warning('Password reset attempted with expired token', ['email' => $email]);
            return [
                'success' => false,
                'message' => 'Password reset link has expired. Please request a new one.',
                'code'    => 'token_expired',
            ];
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        try {
            $user->update([
                'password'                 => Hash::make($password),
                'last_password_changed_at' => now(),
            ]);

            DB::table('password_reset_tokens')->where('email', $email)->delete();

            Log::info('User password reset successfully', [
                'user_id' => $user->id,
                'email'   => $email,
            ]);

            return [
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
                'user'    => $user,
            ];
        } catch (Exception $e) {
            Log::error('Failed to reset password', [
                'email'     => $email,
                'error'     => $e->getMessage(),
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reset password. Please try again later.',
                'user'    => null,
            ];
        }
    }

    /**
     * Get the token expiration time in minutes
     */
    public function getTokenExpiration(): int
    {
        return $this->tokenExpiration;
    }
}
