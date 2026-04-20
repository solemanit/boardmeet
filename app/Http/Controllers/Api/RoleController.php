<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Get all roles with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Role::with('permissions');

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', "%{$request->search}%");
            }

            $roles = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $roles->map(fn($role) => $this->roleResource($role)),
                'pagination' => [
                    'total' => $roles->total(),
                    'per_page' => $roles->perPage(),
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new role
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|integer',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->get('description'),
                'priority' => $request->get('priority', 0),
                'is_active' => true,
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $this->roleResource($role->fresh()),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific role
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'success' => true,
            'role' => $this->roleResource($role->load('permissions')),
        ], 200);
    }

    /**
     * Update a role
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'sometimes|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'sometimes|nullable|string',
            'priority' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role->update($request->only(['name', 'slug', 'description', 'priority', 'is_active']));

            // Update permissions
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $this->roleResource($role->fresh()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a role
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            // Check if role has users
            if ($role->users()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that has assigned users',
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign permissions to a role
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $role->permissions()->sync($request->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully',
                'role' => $this->roleResource($role->fresh()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format role resource
     */
    protected function roleResource(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'priority' => $role->priority,
            'is_active' => $role->is_active,
            'permissions' => $role->permissions->map(fn($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'module' => $permission->module,
            ]),
            'users_count' => $role->users()->count(),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }
}
