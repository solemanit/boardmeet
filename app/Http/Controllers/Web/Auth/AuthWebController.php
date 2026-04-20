<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\PasswordPolicyService;
use App\Services\MFAService;
use App\Services\EmailVerificationService;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    protected PasswordPolicyService $passwordPolicy;
    protected MFAService $mfaService;
    protected EmailVerificationService $emailVerificationService;
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordPolicyService $passwordPolicy, MFAService $mfaService, EmailVerificationService $emailVerificationService, PasswordResetService $passwordResetService)
    {
        $this->passwordPolicy = $passwordPolicy;
        $this->mfaService = $mfaService;
        $this->emailVerificationService = $emailVerificationService;
        $this->passwordResetService = $passwordResetService;
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Account is not activated'])->withInput();
        }

        if (!$user->email_verified_at) {
            return back()->withErrors(['email' => 'Please verify your email before logging in'])->withInput();
        }

        if ($user->isLocked()) {
            return back()->withErrors(['email' => 'Account is locked. Try again later'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            $user->incrementFailedLoginAttempts();
            if ($user->failed_login_attempts >= 5) {
                $user->lockAccount(15);
                return back()->withErrors(['email' => 'Account locked due to too many failed attempts'])->withInput();
            }
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        $user->resetFailedLoginAttempts();

        if ($user->mfa_enabled) {
            $this->mfaService->generateAndSendOTP($user);
            session([
                'user_id' => $user->id,
                'temp_token' => $this->mfaService->generateTempToken($user),
                'mfa_method' => $user->mfa_method,
            ]);
            return redirect()->route('auth.mfa.form');
        }

        $user->activityLogs()->create([
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $user->update(['last_login_at' => now()]);

        Auth::login($user, $request->remember);

        return redirect()->route('dashboard.index')->with('success', 'Logged in successfully!');
    }

    public function verifyMfaForm()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }
        return view('auth.verify-mfa');
    }

    public function verifyMfa(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // FIX: Get user_id from SESSION, not from POST body.
        // Accepting user_id from the form allows anyone to submit
        // any user_id and bypass account ownership entirely.
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Session expired. Please login again.');
        }

        $user = User::findOrFail($userId);

        // FIX: Verify temp_token from session — was completely skipped before.
        // Without this check, a valid OTP alone is enough to log in as any user.
        $sessionTempToken = session('temp_token');
        if (!$sessionTempToken || !$this->mfaService->verifyTempToken($user, $sessionTempToken)) {
            session()->forget(['user_id', 'temp_token', 'mfa_method']);
            return redirect()->route('login')
                ->with('error', 'Invalid or expired MFA session. Please login again.');
        }

        if (!$this->mfaService->verifyOTP($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.'])->withInput();
        }

        $user->activityLogs()->create([
            'action'     => 'login_mfa_verified',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $user->update(['last_login_at' => now()]);

        Auth::login($user);
        session()->forget(['user_id', 'temp_token', 'mfa_method']);

        return redirect()->route('dashboard.index')
            ->with('success', 'Logged in successfully!');
    }

    public function registerForm()
    {
        $roles = Role::where('is_active', true)
            ->whereNotIn('slug', ['admin'])
            ->get();

        return view('auth.register', ['roles' => $roles]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $passwordValidation = $this->passwordPolicy->validate($request->password);
        if (!$passwordValidation['valid']) {
            return back()->withErrors(['password' => 'Password does not meet requirements'])
                ->with('password_requirements', $passwordValidation['requirements'])
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => false,
            'last_password_changed_at' => now(),
        ]);

        // Send verification email
        $this->emailVerificationService->generateAndSendToken($user);

        return redirect()->route('verify-email-pending', ['email' => $user->email])
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->activityLogs()->create([
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->passwordResetService->sendResetLink($request->email);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function resetPasswordForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid password reset link. Missing required parameters.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $passwordValidation = $this->passwordPolicy->validate($request->password);
        if (!$passwordValidation['valid']) {
            return back()->withErrors(['password' => 'Password does not meet requirements'])
                ->with('password_requirements', $passwordValidation['requirements'])
                ->withInput();
        }

        $result = $this->passwordResetService->resetPassword(
            $request->token,
            $request->email,
            $request->password
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('login')
            ->with('success', $result['message']);
    }
}
