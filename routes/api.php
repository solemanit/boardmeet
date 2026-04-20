<?php
// routes/api.php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/auth/verify-mfa', [AuthController::class, 'verifyMFA'])->name('api.auth.verify-mfa');

    // Email verification routes (public)
    Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail'])->name('api.auth.verify-email');
    Route::post('/auth/resend-verification-email', [AuthController::class, 'resendVerificationEmail'])->name('api.auth.resend-verification-email');

    // Password reset routes (public)
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');

    // Protected routes
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Auth routes
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');

        // Admin routes - Users (requires 'admin' or 'md' role)
        Route::middleware('role:admin,md')->prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('api.users.index');
            Route::post('/', [UserController::class, 'store'])->name('api.users.store');
            Route::get('/{user}', [UserController::class, 'show'])->name('api.users.show');
            Route::put('/{user}', [UserController::class, 'update'])->name('api.users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('api.users.destroy');

            // User activation/deactivation
            Route::post('/{user}/activate', [UserController::class, 'activate'])->name('api.users.activate');
            Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('api.users.deactivate');

            // Password reset
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('api.users.reset-password');

            // Activity logs
            Route::get('/{user}/activity', [UserController::class, 'activityLogs'])->name('api.users.activity');
        });

        // Admin routes - Roles (requires 'admin' role)
        Route::middleware('role:admin')->prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('api.roles.index');
            Route::post('/', [RoleController::class, 'store'])->name('api.roles.store');
            Route::get('/{role}', [RoleController::class, 'show'])->name('api.roles.show');
            Route::put('/{role}', [RoleController::class, 'update'])->name('api.roles.update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('api.roles.destroy');

            // Sync permissions to role
            Route::post('/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('api.roles.sync-permissions');
        });

        // Admin routes - Permissions (requires 'admin' role)
        Route::middleware('role:admin')->prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('api.permissions.index');
            Route::get('/modules', [PermissionController::class, 'modules'])->name('api.permissions.modules');
            Route::post('/', [PermissionController::class, 'store'])->name('api.permissions.store');
            Route::get('/{permission}', [PermissionController::class, 'show'])->name('api.permissions.show');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('api.permissions.update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('api.permissions.destroy');
        });

        // Read-only permissions list for all authenticated users
        Route::get('/permissions', [PermissionController::class, 'index'])->name('api.permissions.public');
    });
});
