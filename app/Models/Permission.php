<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'slug',
    'description',
    'module',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Get all roles that have this permission
   */
  public function roles(): BelongsToMany
  {
    return $this->belongsToMany(Role::class, 'role_permission');
  }

  /**
   * Get permissions grouped by module
   */
  public static function groupedByModule()
  {
    return self::where('is_active', true)
      ->get()
      ->groupBy('module');
  }

  /**
   * Get all available modules
   */
  public static function getModules()
  {
    return self::where('is_active', true)
      ->distinct()
      ->pluck('module');
  }
}
