<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Get all permissions with optional grouping by module
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Permission::where('is_active', true);

            // Filter by module
            if ($request->has('module')) {
                $query->where('module', $request->module);
            }

            if ($request->get('group_by_module')) {
                $permissions = $query->get()->groupBy('module');
                return response()->json([
                    'success' => true,
                    'modules' => array_keys($permissions->toArray()),
                    'data' => $permissions->map(fn($items) => $items->map(fn($permission) => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                    ])),
                ], 200);
            }

            $permissions = $query->paginate($request->get('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $permissions->map(fn($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'description' => $permission->description,
                    'module' => $permission->module,
                    'is_active' => $permission->is_active,
                ]),
                'pagination' => [
                    'total' => $permissions->total(),
                    'per_page' => $permissions->perPage(),
                    'current_page' => $permissions->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new permission
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'sometimes|nullable|string',
            'module' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->get('description'),
                'module' => $request->module,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'description' => $permission->description,
                    'module' => $permission->module,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific permission
     */
    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'success' => true,
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'description' => $permission->description,
                'module' => $permission->module,
                'is_active' => $permission->is_active,
                'roles' => $permission->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                ]),
            ],
        ], 200);
    }

    /**
     * Update a permission
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $permission->id,
            'slug' => 'sometimes|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'sometimes|nullable|string',
            'module' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $permission->update($request->only(['name', 'slug', 'description', 'module', 'is_active']));

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'description' => $permission->description,
                    'module' => $permission->module,
                    'is_active' => $permission->is_active,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a permission
     */
    public function destroy(Permission $permission): JsonResponse
    {
        try {
            // Check if permission is assigned to any roles
            if ($permission->roles()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete permission that is assigned to roles',
                ], 422);
            }

            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available modules
     */
    public function modules(): JsonResponse
    {
        try {
            $modules = Permission::where('is_active', true)
                ->distinct()
                ->pluck('module')
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'modules' => $modules,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules: ' . $e->getMessage(),
            ], 500);
        }
    }
}
