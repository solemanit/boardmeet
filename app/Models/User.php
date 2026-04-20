<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
        'is_active',
        'activated_at',
        'mfa_enabled',
        'mfa_method',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
            'phone_verified' => 'boolean',
            'activated_at' => 'datetime',
            'last_password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the user's role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get user activity logs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * Get user's email verification tokens
     */
    public function emailVerificationTokens(): HasMany
    {
        return $this->hasMany(EmailVerificationToken::class);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if user's email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        // This is handled by the EmailVerificationService
    }

    /**
     * Check if email has been verified (MustVerifyEmail interface method)
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->isEmailVerified();
    }

    /**
     * Check if user is locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && now()->isBefore($this->locked_until);
    }

    /**
     * Check if user account is not locked
     */
    public function isNotLocked(): bool
    {
        return !$this->isLocked();
    }

    /**
     * Lock user account for specified minutes
     */
    public function lockAccount(int $minutes = 15): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->slug === $roleName;
    }

    /**
     * Check if user has multiple roles (This will be extended for future multi-role support)
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->role && in_array($this->role->slug, $roleNames);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->role && $this->role->hasPermission($permissionSlug);
    }

    /**
     * Check if user has multiple permissions
     */
    public function hasPermissions(array $permissionSlugs): bool
    {
        return $this->role && $this->role->hasPermissions($permissionSlugs);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->role && $this->role->hasAnyPermission($permissionSlugs);
    }

    /**
     * Get user's permissions
     */
    public function getPermissions()
    {
        return $this->role ? $this->role->permissions : collect();
    }

    /**
     * Activate user account
     */
    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
        ]);
    }
}
