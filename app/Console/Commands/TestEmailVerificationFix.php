<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Console\Command;

class TestEmailVerificationFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-verification-fix {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the email verification fix to ensure email_verified_at is properly set';

    /**
     * Email verification service
     */
    protected EmailVerificationService $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        parent::__construct();
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: {$email}");
            return 1;
        }

        $this->info('=== Email Verification Test ===');
        $this->line("User: {$user->name} ({$user->email})");
        $this->line("Current Status:");
        $this->line("  - ID: {$user->id}");
        $this->line("  - is_active: " . ($user->is_active ? 'TRUE' : 'FALSE'));
        $this->line("  - email_verified_at: " . ($user->email_verified_at ?? 'NULL'));

        // Generate a new token
        $token = $this->emailVerificationService->generateAndSendToken($user);

        $this->info("\nToken Generated:");
        $this->line("  - Token: {$token->token}");
        $this->line("  - Expires at: {$token->expires_at}");

        $this->line("\nVerification Link:");
        $link = route('verify-email', [
            'token' => $token->token,
            'email' => $user->email,
        ]);
        $this->line("  " . $link);

        $this->line("\n=== Simulating Email Verification ===");
        $result = $this->emailVerificationService->verifyEmail($token->token, $user->email);

        $this->line("Result: " . ($result['success'] ? 'SUCCESS ✓' : 'FAILED ✗'));
        $this->line("Message: {$result['message']}");

        // Refresh user from database
        $user = $user->fresh();

        $this->line("\n=== Updated Status ===");
        $this->line("User: {$user->name} ({$user->email})");
        $this->line("  - is_active: " . ($user->is_active ? 'TRUE ✓' : 'FALSE ✗'));
        $this->line("  - email_verified_at: " . ($user->email_verified_at ?? 'NULL ✗'));
        $this->line("  - activated_at: " . ($user->activated_at ?? 'NULL'));

        if ($result['success'] && $user->email_verified_at && $user->is_active) {
            $this->info("\n✓ All checks passed! User can now login.");
            return 0;
        } else {
            $this->error("\n✗ Some checks failed!");
            return 1;
        }
    }
}
