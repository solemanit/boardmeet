<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TestEmailVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-verification {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email verification by sending a verification email to a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info("Testing email verification for: {$email}");

        // Find or create user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->warn("User not found with email: {$email}");
            $this->info("Creating test user...");

            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => bcrypt('password'),
                'role_id' => 1,
                'is_active' => true,
            ]);

            $this->info("Test user created with ID: {$user->id}");
        }

        // Delete existing verification tokens
        EmailVerificationToken::where('user_id', $user->id)->delete();

        // Create new token
        $this->info("Creating verification token...");
        $token = EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->info("Token created: {$token->token}");

        // Try to send email
        $this->info("Attempting to send verification email...");

        try {
            $token->load('user');
            $user->notify(new VerifyEmailNotification($token));

            $this->info("✓ Email sent successfully!");
            $this->line("Verification URL: " . config('app.app_url') . "/verify-email?token={$token->token}&email=" . urlencode($user->email));

            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email!");
            $this->error("Error: " . $e->getMessage());
            $this->line("Stack trace:");
            $this->line($e->getTraceAsString());

            Log::error('Email verification test failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            return 1;
        }
    }
}
