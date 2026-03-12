<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('permissions');

        $roles = [];
        foreach ($config['roles'] as $roleName) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        // Permisos globales
        foreach ($config['global_permissions'] as $perm) {
            Permission::firstOrCreate(['name' => $perm])
                ->syncRoles([$roles['super_root'], $roles['Administrador']]);
        }

        // CRUD por vistas
        foreach ($config['views'] as $view) {
            foreach (['index', 'create', 'edit', 'show', 'destroy'] as $action) {

                $permission = Permission::firstOrCreate([
                    'name' => "$view.$action"
                ]);

                // Solo roles definidos reciben los CRUD
                $assignTo = array_map(
                    fn ($r) => $roles[$r],
                    $config['crud_default_roles']
                );

                $permission->syncRoles($assignTo);
            }
        }
    }
}
