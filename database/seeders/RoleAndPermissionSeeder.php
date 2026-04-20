<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create permissions
    $permissions = [
      // User Management
      ['name' => 'View Users', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Can view list of users'],
      ['name' => 'Create Users', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Can create new users'],
      ['name' => 'Edit Users', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Can edit user details'],
      ['name' => 'Delete Users', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Can delete users'],
      ['name' => 'Activate Users', 'slug' => 'users.activate', 'module' => 'users', 'description' => 'Can activate user accounts'],
      ['name' => 'Deactivate Users', 'slug' => 'users.deactivate', 'module' => 'users', 'description' => 'Can deactivate user accounts'],
      ['name' => 'Reset User Password', 'slug' => 'users.reset_password', 'module' => 'users', 'description' => 'Can reset user passwords'],
      ['name' => 'View User Activity', 'slug' => 'users.view_activity', 'module' => 'users', 'description' => 'Can view user activity logs'],

      // Role Management
      ['name' => 'View Roles', 'slug' => 'roles.view', 'module' => 'roles', 'description' => 'Can view list of roles'],
      ['name' => 'Create Roles', 'slug' => 'roles.create', 'module' => 'roles', 'description' => 'Can create new roles'],
      ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'module' => 'roles', 'description' => 'Can edit role details'],
      ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'module' => 'roles', 'description' => 'Can delete roles'],
      ['name' => 'Manage Role Permissions', 'slug' => 'roles.manage_permissions', 'module' => 'roles', 'description' => 'Can assign/revoke permissions to roles'],

      // Permission Management
      ['name' => 'View Permissions', 'slug' => 'permissions.view', 'module' => 'permissions', 'description' => 'Can view list of permissions'],
      ['name' => 'Create Permissions', 'slug' => 'permissions.create', 'module' => 'permissions', 'description' => 'Can create new permissions'],
      ['name' => 'Edit Permissions', 'slug' => 'permissions.edit', 'module' => 'permissions', 'description' => 'Can edit permission details'],
      ['name' => 'Delete Permissions', 'slug' => 'permissions.delete', 'module' => 'permissions', 'description' => 'Can delete permissions'],

      // Board Management
      ['name' => 'View Board Meetings', 'slug' => 'board.view_meetings', 'module' => 'board', 'description' => 'Can view board meetings'],
      ['name' => 'Create Board Meetings', 'slug' => 'board.create_meetings', 'module' => 'board', 'description' => 'Can create board meetings'],
      ['name' => 'Edit Board Meetings', 'slug' => 'board.edit_meetings', 'module' => 'board', 'description' => 'Can edit board meetings'],
      ['name' => 'Delete Board Meetings', 'slug' => 'board.delete_meetings', 'module' => 'board', 'description' => 'Can delete board meetings'],
      ['name' => 'View Board Minutes', 'slug' => 'board.view_minutes', 'module' => 'board', 'description' => 'Can view board meeting minutes'],
      ['name' => 'Create Board Minutes', 'slug' => 'board.create_minutes', 'module' => 'board', 'description' => 'Can create board meeting minutes'],

      // Company Management
      ['name' => 'View Company Info', 'slug' => 'company.view', 'module' => 'company', 'description' => 'Can view company information'],
      ['name' => 'Edit Company Info', 'slug' => 'company.edit', 'module' => 'company', 'description' => 'Can edit company information'],

      // Reports
      ['name' => 'View Reports', 'slug' => 'reports.view', 'module' => 'reports', 'description' => 'Can view reports'],
      ['name' => 'Generate Reports', 'slug' => 'reports.generate', 'module' => 'reports', 'description' => 'Can generate reports'],
    ];

    foreach ($permissions as $permission) {
      Permission::updateOrCreate(
        ['slug' => $permission['slug']],
        $permission
      );
    }

    // Create roles
    $roles = [
      [
        'name' => 'Admin',
        'slug' => 'admin',
        'description' => 'System Administrator - Full access',
        'priority' => 100,
      ],
      [
        'name' => 'Managing Director',
        'slug' => 'md',
        'description' => 'Managing Director - High level access',
        'priority' => 80,
      ],
      [
        'name' => 'Chairman',
        'slug' => 'chairman',
        'description' => 'Board Chairman - Board and company level access',
        'priority' => 75,
      ],
      [
        'name' => 'Company Secretary',
        'slug' => 'company_secretary',
        'description' => 'Company Secretary - Board and company management',
        'priority' => 70,
      ],
      [
        'name' => 'Board Member',
        'slug' => 'board_member',
        'description' => 'Board Member - Read-only board access',
        'priority' => 50,
      ],
    ];

    foreach ($roles as $roleData) {
      Role::updateOrCreate(
        ['slug' => $roleData['slug']],
        $roleData
      );
    }

    // Assign permissions to roles
    $adminPermissions = Permission::pluck('id')->toArray();
    Role::where('slug', 'admin')->first()->permissions()->sync($adminPermissions);

    $mdPermissions = Permission::whereIn('slug', [
      'users.view',
      'users.edit',
      'users.activate',
      'users.deactivate',
      'users.view_activity',
      'roles.view',
      'permissions.view',
      'board.view_meetings',
      'board.create_meetings',
      'board.edit_meetings',
      'board.view_minutes',
      'board.create_minutes',
      'company.view',
      'reports.view',
      'reports.generate',
    ])->pluck('id')->toArray();
    Role::where('slug', 'md')->first()->permissions()->sync($mdPermissions);

    $chairmanPermissions = Permission::whereIn('slug', [
      'board.view_meetings',
      'board.create_meetings',
      'board.edit_meetings',
      'board.view_minutes',
      'board.create_minutes',
      'company.view',
      'company.edit',
      'reports.view',
    ])->pluck('id')->toArray();
    Role::where('slug', 'chairman')->first()->permissions()->sync($chairmanPermissions);

    $secretaryPermissions = Permission::whereIn('slug', [
      'users.view',
      'board.view_meetings',
      'board.create_meetings',
      'board.edit_meetings',
      'board.view_minutes',
      'board.create_minutes',
      'company.view',
      'company.edit',
      'reports.view',
      'reports.generate',
    ])->pluck('id')->toArray();
    Role::where('slug', 'company_secretary')->first()->permissions()->sync($secretaryPermissions);

    $boardMemberPermissions = Permission::whereIn('slug', [
      'board.view_meetings',
      'board.view_minutes',
      'company.view',
      'reports.view',
    ])->pluck('id')->toArray();
    Role::where('slug', 'board_member')->first()->permissions()->sync($boardMemberPermissions);
  }
}
