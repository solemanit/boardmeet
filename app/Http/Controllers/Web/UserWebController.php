<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\PasswordPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserWebController extends Controller
{
  protected PasswordPolicyService $passwordPolicy;

  public function __construct(PasswordPolicyService $passwordPolicy)
  {
    $this->passwordPolicy = $passwordPolicy;
  }

  public function index(Request $request)
  {
    $query = User::with('role');

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%");
      });
    }

    if ($request->filled('role_id')) {
      $query->where('role_id', $request->role_id);
    }

    if ($request->filled('is_active')) {
      $query->where('is_active', $request->is_active);
    }

    $users = $query->paginate(15);
    $roles = Role::all();

    return view('users.index', compact('users', 'roles'));
  }

  public function create()
  {
    $roles = Role::where('is_active', true)->whereNotIn('slug', ['admin'])->get();
    return view('users.create', compact('roles'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users',
      'password' => 'required|string|min:8',
      'role_id' => 'required|exists:roles,id',
    ]);

    $passwordValidation = $this->passwordPolicy->validate($request->password);
    if (!$passwordValidation['valid']) {
      return back()->withErrors(['password' => 'Password does not meet requirements'])->withInput();
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role_id' => $request->role_id,
      'is_active' => true,
      'activated_at' => now(),
      'last_password_changed_at' => now(),
    ]);

    return redirect()->route('users.show', $user)->with('success', 'User created successfully!');
  }

  public function show(User $user)
  {
    $user->load('role', 'activityLogs');
    return view('users.show', compact('user'));
  }

  public function edit(User $user)
  {
    $roles = Role::where('is_active', true)->get();
    return view('users.edit', compact('user', 'roles'));
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $user->id,
      'role_id' => 'required|exists:roles,id',
      'phone_number' => 'nullable|string|max:20',
    ]);

    $user->update($request->only('name', 'email', 'role_id', 'phone_number'));

    return redirect()->route('users.show', $user)->with('success', 'User updated successfully!');
  }

  public function destroy(User $user)
  {
    $user->delete();
    return redirect()->route('users.index')->with('success', 'User deleted successfully!');
  }

  public function activate(User $user)
  {
    $user->activate();
    return redirect()->back()->with('success', 'User activated successfully!');
  }

  public function deactivate(User $user)
  {
    if ($user->role->slug === 'admin') {
      $activeAdmins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))
        ->where('is_active', true)->count();
      if ($activeAdmins === 1) {
        return redirect()->back()->with('error', 'Cannot deactivate the last active admin!');
      }
    }

    $user->deactivate();
    return redirect()->back()->with('success', 'User deactivated successfully!');
  }

  public function resetPassword(Request $request, User $user)
  {
    $request->validate([
      'password' => 'required|string|min:8',
    ]);

    $passwordValidation = $this->passwordPolicy->validate($request->password);
    if (!$passwordValidation['valid']) {
      return redirect()->back()->withErrors(['password' => 'Password does not meet requirements']);
    }

    $user->update([
      'password' => Hash::make($request->password),
      'last_password_changed_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Password reset successfully!');
  }
}
