<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordPolicyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected PasswordPolicyService $passwordPolicy;

    public function __construct(PasswordPolicyService $passwordPolicy)
    {
        $this->passwordPolicy = $passwordPolicy;
    }

    /**
     * Get all users with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with('role');

            // Filter by role
            if ($request->has('role_id')) {
                $query->where('role_id', $request->role_id);
            }

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            // Search by name or email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users->map(fn($user) => $this->userResource($user)),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate password policy
        $passwordValidation = $this->passwordPolicy->validate($request->password);
        if (!$passwordValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Password does not meet policy requirements',
                'requirements' => $passwordValidation['requirements'],
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_active' => $request->get('is_active', false),
                'last_password_changed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $this->userResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific user
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $this->userResource($user->load('role')),
        ], 200);
    }

    /**
     * Update user details
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role_id' => 'sometimes|exists:roles,id',
            'phone_number' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $updateData = $request->only(['name', 'email', 'role_id', 'phone_number']);
            $user->update(array_filter($updateData));

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $this->userResource($user->fresh()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate user account
     */
    public function activate(User $user): JsonResponse
    {
        try {
            $user->activate();

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully',
                'user' => $this->userResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate user account
     */
    public function deactivate(User $user): JsonResponse
    {
        try {
            // Cannot deactivate admin if they're the last active admin
            if ($user->role->slug === 'admin') {
                $activeAdmins = User::whereHas('role', function ($q) {
                    $q->where('slug', 'admin');
                })->where('is_active', true)->count();

                if ($activeAdmins === 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot deactivate the last active admin user',
                    ], 422);
                }
            }

            $user->deactivate();

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully',
                'user' => $this->userResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate password policy
        $passwordValidation = $this->passwordPolicy->validate($request->password);
        if (!$passwordValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Password does not meet policy requirements',
                'requirements' => $passwordValidation['requirements'],
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
                'last_password_changed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user activity logs
     */
    public function activityLogs(User $user): JsonResponse
    {
        try {
            $logs = $user->activityLogs()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'logs' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve activity logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format user resource
     */
    protected function userResource(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : null,
            'permissions' => $user->getPermissions()->pluck('slug')->toArray(),
            'is_active' => $user->is_active,
            'activated_at' => $user->activated_at,
            'mfa_enabled' => $user->mfa_enabled,
            'mfa_method' => $user->mfa_method,
            'phone_verified' => $user->phone_verified,
            'last_login_at' => $user->last_login_at,
            'last_password_changed_at' => $user->last_password_changed_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
