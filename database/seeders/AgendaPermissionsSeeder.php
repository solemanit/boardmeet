<?php

// ── AgendaPermissionsSeeder.php ─────────────────────────────────────
// Run: php artisan db:seed --class=AgendaPermissionsSeeder

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AgendaPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Agendas',    'slug' => 'agendas.view',    'module' => 'agendas', 'description' => 'View agenda list and details'],
            ['name' => 'Create Agendas',  'slug' => 'agendas.create',  'module' => 'agendas', 'description' => 'Create new agenda items'],
            ['name' => 'Edit Agendas',    'slug' => 'agendas.edit',    'module' => 'agendas', 'description' => 'Edit draft/rejected agendas'],
            ['name' => 'Delete Agendas',  'slug' => 'agendas.delete',  'module' => 'agendas', 'description' => 'Delete agenda items'],
            ['name' => 'Submit Agendas',  'slug' => 'agendas.submit',  'module' => 'agendas', 'description' => 'Submit agendas for approval'],
            ['name' => 'Approve Agendas', 'slug' => 'agendas.approve', 'module' => 'agendas', 'description' => 'Approve or reject agendas'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                array_merge($permission, ['is_active' => true])
            );
        }

        $this->command->info('Agenda permissions seeded successfully!');
    }
}
