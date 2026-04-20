<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordPolicyService;
use App\Services\MFAService;
use App\Services\EmailVerificationService;
use App\Services\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected PasswordPolicyService $passwordPolicy;
    protected MFAService $mfaService;
    protected EmailVerificationService $emailVerificationService;
    protected PasswordResetService $passwordResetService;

    public function __construct(
        PasswordPolicyService $passwordPolicy,
        MFAService $mfaService,
        EmailVerificationService $emailVerificationService,
        PasswordResetService $passwordResetService
    ) {
        $this->passwordPolicy = $passwordPolicy;
        $this->mfaService = $mfaService;
        $this->emailVerificationService = $emailVerificationService;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * User registration
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'role_id'               => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $passwordValidation = $this->passwordPolicy->validate($request->password);
        if (!$passwordValidation['valid']) {
            return response()->json([
                'success'      => false,
                'message'      => 'Password does not meet policy requirements',
                'requirements' => $passwordValidation['failed_requirements'],
            ], 422);
        }

        try {
            $user = User::create([
                'name'                    => $request->name,
                'email'                   => $request->email,
                'password'                => Hash::make($request->password),
                'role_id'                 => $request->role_id,
                'is_active'               => false,
                'last_password_changed_at' => now(),
            ]);

            $this->emailVerificationService->generateAndSendToken($user);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully. Please verify your email before login.',
                'user_id' => $user->id,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * User login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is not activated. Please contact administrator.',
                ], 403);
            }

            if ($user->isLocked()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is locked due to too many failed login attempts. Try again later.',
                ], 429);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                $user->incrementFailedLoginAttempts();

                if ($user->failed_login_attempts >= 5) {
                    $user->lockAccount(15);
                    return response()->json([
                        'success' => false,
                        'message' => 'Account locked due to too many failed login attempts. Try again in 15 minutes.',
                    ], 429);
                }

                return response()->json([
                    'success'           => false,
                    'message'           => 'Invalid credentials',
                    'attempts_remaining' => 5 - $user->failed_login_attempts,
                ], 401);
            }

            if (!$user->isEmailVerified()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email address before logging in.',
                    'code'    => 'email_not_verified',
                    'user_id' => $user->id,
                    'email'   => $user->email,
                ], 403);
            }

            // Reset failed attempts only after all checks pass
            $user->resetFailedLoginAttempts();

            if ($user->mfa_enabled) {
                $this->mfaService->generateAndSendOTP($user);

                return response()->json([
                    'success'      => true,
                    'message'      => 'OTP sent to your ' . $user->mfa_method,
                    'mfa_required' => true,
                    'user_id'      => $user->id,
                    'temp_token'   => $this->mfaService->generateTempToken($user),
                ], 200);
            }

            $this->recordActivity($user, 'login');
            $user->update(['last_login_at' => now()]);
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user'    => $this->userResource($user),
                'token'   => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify MFA OTP
     */
    public function verifyMFA(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'otp'        => 'required|string|size:6',
            'temp_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($request->user_id);

            // FIX #2: Actually verify the temp_token — was completely skipped before,
            // allowing anyone with a valid user_id + OTP to bypass the session check.
            if (!$this->mfaService->verifyTempToken($user, $request->temp_token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired MFA session. Please login again.',
                ], 401);
            }

            if (!$this->mfaService->verifyOTP($user, $request->otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 401);
            }

            $this->recordActivity($user, 'login_mfa');
            $user->update(['last_login_at' => now()]);
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'MFA verification successful',
                'user'    => $this->userResource($user),
                'token'   => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'MFA verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $this->recordActivity($user, 'logout');
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user'    => $this->userResource($request->user()),
        ], 200);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 401);
            }

            $passwordValidation = $this->passwordPolicy->validate($request->new_password);
            if (!$passwordValidation['valid']) {
                return response()->json([
                    'success'      => false,
                    'message'      => 'New password does not meet policy requirements',
                    'requirements' => $passwordValidation['failed_requirements'],
                ], 422);
            }

            $user->update([
                'password'                 => Hash::make($request->new_password),
                'last_password_changed_at' => now(),
            ]);

            $this->recordActivity($user, 'password_change');

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->emailVerificationService->verifyEmail($request->token, $request->email);

            if (!$result['success']) {
                $statusCode = ($result['code'] ?? null) === 'token_expired' ? 410 : 400;
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code'    => $result['code'] ?? null,
                ], $statusCode);
            }

            $this->recordActivity($result['user'], 'email_verified');

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'user'    => $this->userResource($result['user']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $result = $this->emailVerificationService->resendVerificationEmail($user);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

            $this->recordActivity($user, 'resend_verification_email');

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend verification email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->passwordResetService->sendResetLink($request->email);

            if (!$result['success']) {
                // 404 if user not found, 503 if mail send failed
                $statusCode = empty($result['user']) ? 404 : 503;
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], $statusCode);
            }

            // FIX #1: Guard against missing 'user' key — service returns null user
            // when the email doesn't exist (security: we don't reveal that to client).
            if (!empty($result['user'])) {
                $this->recordActivity($result['user'], 'password_reset_requested');
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset link: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $passwordValidation = $this->passwordPolicy->validate($request->password);
            if (!$passwordValidation['valid']) {
                return response()->json([
                    'success'      => false,
                    'message'      => 'Password does not meet policy requirements',
                    'requirements' => $passwordValidation['failed_requirements'],
                ], 422);
            }

            $result = $this->passwordResetService->resetPassword(
                $request->token,
                $request->email,
                $request->password
            );

            if (!$result['success']) {
                $statusCode = ($result['code'] ?? null) === 'token_expired' ? 410 : 400;
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'code'    => $result['code'] ?? null,
                ], $statusCode);
            }

            $this->recordActivity($result['user'], 'password_reset');

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record user activity
     */
    protected function recordActivity(User $user, string $action): void
    {
        $user->activityLogs()->create([
            'action'     => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'details'    => ['timestamp' => now()],
        ]);
    }

    /**
     * Format user resource
     */
    protected function userResource(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role ? [
                'id'   => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : null,
            'permissions' => $user->getPermissions()->pluck('slug')->toArray(),
            'is_active'   => $user->is_active,
            'mfa_enabled' => $user->mfa_enabled,
            'created_at'  => $user->created_at,
        ];
    }
}
