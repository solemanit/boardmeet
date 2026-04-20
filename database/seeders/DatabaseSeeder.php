<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RoleAndPermissionSeeder::class,
        ]);

        // Create an admin user
        $adminRole = Role::where('slug', 'admin')->first();
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@boardmeet.local',
            'password' => bcrypt('Password@123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
            'activated_at' => now(),
            'last_password_changed_at' => now(),
        ]);

        // Create sample users with different roles
        $roles = Role::whereIn('slug', ['md', 'chairman', 'company_secretary', 'board_member'])->get();
        foreach ($roles as $role) {
            User::factory()->create([
                'role_id' => $role->id,
                'is_active' => true,
                'activated_at' => now(),
                'last_password_changed_at' => now(),
            ]);
        }
    }
}
