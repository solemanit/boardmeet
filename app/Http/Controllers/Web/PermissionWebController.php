<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionWebController extends Controller
{
  public function index(Request $request)
  {
    $query = Permission::query();

    if ($request->filled('module')) {
      $query->where('module', $request->module);
    }

    if ($request->filled('search')) {
      $query->where('name', 'like', "%{$request->search}%");
    }

    $permissions = $query->paginate(20);
    $modules = Permission::distinct()->pluck('module')->filter();
    $totalCount = Permission::count();

    return view('permissions.index', compact('permissions', 'modules', 'totalCount'));
  }

  public function create()
  {
    $modules = Permission::distinct()->pluck('module')->filter()->sort();
    return view('permissions.create', compact('modules'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:permissions',
      'slug' => 'required|string|max:255|unique:permissions',
      'description' => 'nullable|string',
      'module' => 'required|string|max:255',
    ]);

    $permission = Permission::create([
      'name' => $request->name,
      'slug' => $request->slug,
      'description' => $request->description,
      'module' => $request->module,
      'is_active' => true,
    ]);

    return redirect()->route('permissions.show', $permission)->with('success', 'Permission created successfully!');
  }

  public function show(Permission $permission)
  {
    $permission->load('roles');
    return view('permissions.show', compact('permission'));
  }

  public function edit(Permission $permission)
  {
    $modules = Permission::distinct()->pluck('module')->filter()->sort();
    return view('permissions.edit', compact('permission', 'modules'));
  }

  public function update(Request $request, Permission $permission)
  {
    $request->validate([
      'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
      'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
      'description' => 'nullable|string',
      'module' => 'required|string|max:255',
      'is_active' => 'boolean',
    ]);

    $permission->update($request->only('name', 'slug', 'description', 'module', 'is_active'));

    return redirect()->route('permissions.show', $permission)->with('success', 'Permission updated successfully!');
  }

  public function destroy(Permission $permission)
  {
    if ($permission->roles()->exists()) {
      return redirect()->back()->with('error', 'Cannot delete permission assigned to roles!');
    }

    $permission->delete();
    return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
  }
}
