<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'slug',
    'description',
    'priority',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'priority' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Get all permissions associated with this role
   */
  public function permissions(): BelongsToMany
  {
    return $this->belongsToMany(Permission::class, 'role_permission');
  }

  /**
   * Get all users with this role
   */
  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }

  /**
   * Check if role has a specific permission
   */
  public function hasPermission(string $permissionSlug): bool
  {
    return $this->permissions()
      ->where('slug', $permissionSlug)
      ->where('is_active', true)
      ->exists();
  }

  /**
   * Check if role has multiple permissions
   */
  public function hasPermissions(array $permissionSlugs): bool
  {
    return $this->permissions()
      ->whereIn('slug', $permissionSlugs)
      ->where('is_active', true)
      ->count() === count($permissionSlugs);
  }

  /**
   * Check if role has any of the given permissions
   */
  public function hasAnyPermission(array $permissionSlugs): bool
  {
    return $this->permissions()
      ->whereIn('slug', $permissionSlugs)
      ->where('is_active', true)
      ->exists();
  }
}
