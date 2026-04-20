<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleWebController extends Controller
{
  public function index()
  {
    $roles = Role::with('permissions')->paginate(15);
    return view('roles.index', compact('roles'));
  }

  public function create()
  {
    $permissions = Permission::where('is_active', true)->get();
    $permissionsByModule = $permissions->groupBy('module');
    return view('roles.create', compact('permissionsByModule'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:roles',
      'slug' => 'required|string|max:255|unique:roles',
      'description' => 'nullable|string',
      'priority' => 'nullable|integer',
      'permissions' => 'array',
    ]);

    $role = Role::create([
      'name' => $request->name,
      'slug' => $request->slug,
      'description' => $request->description,
      'priority' => $request->priority ?? 0,
      'is_active' => true,
    ]);

    if ($request->has('permissions')) {
      $role->permissions()->sync($request->permissions);
    }

    return redirect()->route('roles.show', $role)->with('success', 'Role created successfully!');
  }

  public function show(Role $role)
  {
    $role->load('permissions');
    return view('roles.show', compact('role'));
  }

  public function edit(Role $role)
  {
    $permissions = Permission::where('is_active', true)->get();
    $permissionsByModule = $permissions->groupBy('module');
    $role->load('permissions');
    return view('roles.edit', compact('role', 'permissionsByModule'));
  }

  public function update(Request $request, Role $role)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
      'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
      'description' => 'nullable|string',
      'priority' => 'nullable|integer',
      'permissions' => 'array',
    ]);

    $role->update([
      'name' => $request->name,
      'slug' => $request->slug,
      'description' => $request->description,
      'priority' => $request->priority ?? 0,
    ]);

    if ($request->has('permissions')) {
      $role->permissions()->sync($request->permissions);
    }

    return redirect()->route('roles.show', $role)->with('success', 'Role updated successfully!');
  }

  public function destroy(Role $role)
  {
    if ($role->users()->exists()) {
      return redirect()->back()->with('error', 'Cannot delete role with assigned users!');
    }

    $role->delete();
    return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
  }

  public function syncPermissions(Request $request, Role $role)
  {
    $request->validate([
      'permissions' => 'array',
    ]);

    $role->permissions()->sync($request->permissions ?? []);
    return redirect()->back()->with('success', 'Permissions updated successfully!');
  }
}
