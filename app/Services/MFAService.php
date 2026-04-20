<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MFAService
{
    protected int $otpLength            = 6;
    protected int $otpExpirationMinutes = 5;

    /**
     * Generate OTP and send to user via specified method
     */
    public function generateAndSendOTP(User $user): string
    {
        $otp = $this->generateOTP();

        Cache::put(
            "otp.{$user->id}",
            $otp,
            now()->addMinutes($this->otpExpirationMinutes)
        );

        if ($user->mfa_method === 'sms' && $user->phone_number) {
            $this->sendSMSOTP($user, $otp);
        } elseif ($user->mfa_method === 'totp') {
            // TOTP: user generates from authenticator app, nothing to send
        } else {
            // Default: email OTP
            $this->sendEmailOTP($user, $otp);
        }

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(User $user, string $otp): bool
    {
        if ($user->mfa_method === 'totp') {
            return $this->verifyTOTP($user, $otp);
        }

        $cachedOTP = Cache::get("otp.{$user->id}");

        if (!$cachedOTP || $cachedOTP !== $otp) {
            return false;
        }

        Cache::forget("otp.{$user->id}");

        return true;
    }

    /**
     * Generate temporary token for MFA flow
     */
    public function generateTempToken(User $user): string
    {
        $token = Str::random(32);
        Cache::put(
            "mfa_temp.{$user->id}",
            $token,
            now()->addMinutes(15)
        );

        return $token;
    }

    /**
     * FIX #2: This method existed but was never called in AuthController::verifyMFA().
     * It is now called as the FIRST check before verifying the OTP.
     * Without this, anyone knowing a valid user_id + OTP could skip the temp token entirely.
     */
    public function verifyTempToken(User $user, string $token): bool
    {
        $cachedToken = Cache::get("mfa_temp.{$user->id}");

        if (!$cachedToken || $cachedToken !== $token) {
            return false;
        }

        // Consume the token — single use only
        Cache::forget("mfa_temp.{$user->id}");

        return true;
    }

    /**
     * Generate TOTP secret for authenticator app
     */
    public function generateTOTPSecret(): string
    {
        return bin2hex(random_bytes(20));
    }

    /**
     * Enable TOTP for user
     */
    public function enableTOTP(User $user, string $method = 'totp'): array
    {
        $secret = $this->generateTOTPSecret();

        $user->update([
            'mfa_enabled' => true,
            'mfa_method'  => $method,
            'mfa_secret'  => $secret,
        ]);

        $issuer      = config('app.name', 'BoardMeet');
        $accountName = $user->email;
        $qrCodeUrl   = "otpauth://totp/{$issuer}:{$accountName}?secret={$secret}&issuer={$issuer}";

        return [
            'secret'      => $secret,
            'qr_code_url' => $qrCodeUrl,
        ];
    }

    /**
     * Disable MFA for user
     */
    public function disableMFA(User $user): void
    {
        $user->update([
            'mfa_enabled' => false,
            'mfa_method'  => null,
            'mfa_secret'  => null,
        ]);
    }

    /**
     * Verify TOTP token
     */
    protected function verifyTOTP(User $user, string $code): bool
    {
        if (!$user->mfa_secret) {
            return false;
        }

        $secret = $this->base32Decode($user->mfa_secret);
        $time   = floor(time() / 30);

        for ($i = -1; $i <= 1; $i++) {
            $hash   = hash_hmac('sha1', pack('N*', 0, $time + $i), $secret, true);
            $offset = ord(substr($hash, -1)) & 0x0F;
            $otp    = (((ord(substr($hash, $offset, 1)) & 0x7F) << 24)
                | ((ord(substr($hash, $offset + 1, 1)) & 0xFF) << 16)
                | ((ord(substr($hash, $offset + 2, 1)) & 0xFF) << 8)
                | (ord(substr($hash, $offset + 3, 1)) & 0xFF)) % 1000000;

            if (str_pad($otp, 6, '0', STR_PAD_LEFT) === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate random OTP
     */
    protected function generateOTP(): string
    {
        return str_pad(
            random_int(0, 999999),
            $this->otpLength,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Send OTP via email
     */
    protected function sendEmailOTP(User $user, string $otp): void
    {
        info("OTP for user {$user->email}: {$otp}");
    }

    /**
     * Send OTP via SMS
     */
    protected function sendSMSOTP(User $user, string $otp): void
    {
        info("SMS OTP for user {$user->phone_number}: {$otp}");
    }

    /**
     * Decode base32 string
     */
    protected function base32Decode(string $str): string
    {
        $alphabet     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $str          = strtoupper($str);
        $binaryString = '';

        foreach (str_split($str) as $char) {
            $pos = strpos($alphabet, $char);
            if ($pos === false) {
                throw new \Exception('Invalid character in base32 string');
            }
            $binaryString .= str_pad(base_convert($pos, 10, 2), 5, '0', STR_PAD_LEFT);
        }

        $eightBits = str_split($binaryString, 8);
        $decoded   = '';

        foreach ($eightBits as $binary) {
            if (strlen($binary) === 8) {
                $decoded .= chr(base_convert($binary, 2, 10));
            }
        }

        return $decoded;
    }
}
