<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Http\Request;

class VerifyEmailWebController extends Controller
{
    /**
     * Email verification service
     */
    protected EmailVerificationService $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Show email verification form or verify email
     */
    public function verify(Request $request)
    {
        // Get token and email from query parameters
        $token = $request->query('token');
        $email = $request->query('email');

        // If no parameters, show error
        if (!$token || !$email) {
            return view('auth.verify-email-error', [
                'message' => 'Invalid verification link. Missing required parameters.',
            ]);
        }

        // Verify the email
        $result = $this->emailVerificationService->verifyEmail($token, $email);

        if ($result['success']) {
            // Email verified successfully
            return view('auth.verify-email-success', [
                'user' => $result['user'],
                'message' => 'Your email has been verified successfully! You can now login.',
            ]);
        }

        // Email verification failed
        $errorCode = $result['code'] ?? null;

        if ($errorCode === 'token_expired') {
            return view('auth.verify-email-error', [
                'message' => $result['message'],
                'code' => 'token_expired',
                'email' => $email,
            ]);
        }

        return view('auth.verify-email-error', [
            'message' => $result['message'],
            'email' => $email,
        ]);
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $result = $this->emailVerificationService->resendVerificationEmail($user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
