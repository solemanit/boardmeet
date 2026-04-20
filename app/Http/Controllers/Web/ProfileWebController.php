<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PasswordPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileWebController extends Controller
{
    protected PasswordPolicyService $passwordPolicy;

    public function __construct(PasswordPolicyService $passwordPolicy)
    {
        $this->passwordPolicy = $passwordPolicy;
    }

    public function index()
    {
        $user = auth()->user()->load('role', 'activityLogs');
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone_number' => 'nullable|string|max:20',
        ];

        // If password change is requested
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|current_password';
            $rules['new_password'] = 'required|string|min:8|confirmed';
            $rules['new_password_confirmation'] = 'required|string|min:8';
        }

        $request->validate($rules);

        // Check password policy if new password is provided
        if ($request->filled('new_password')) {
            $passwordValidation = $this->passwordPolicy->validate($request->new_password);
            if (!$passwordValidation['valid']) {
                return back()->withErrors(['new_password' => 'Password does not meet requirements: ' . implode(', ', $passwordValidation['failed_requirements'])]);
            }

            auth()->user()->update([
                'password' => Hash::make($request->new_password),
                'last_password_changed_at' => now(),
            ]);
        }

        auth()->user()->update($request->only('name', 'email', 'phone_number'));

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // In a real app, upload the avatar and store the path
        // For now, we'll just return success
        return redirect()->back()->with('success', 'Avatar updated successfully!');
    }
}
