<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class RefreshPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refrescar todos los permisos manteniendo los roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Refrescando permisos...');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::transaction(function () {

            $config = config('permissions');

            // Mantener roles existentes
            $existing = Role::all()->keyBy('name');

            // Crear o cargar roles
            $roles = [];
            foreach ($config['roles'] as $r) {
                $roles[$r] = $existing[$r] ?? Role::create(['name' => $r]);
            }

            // Limpiar permisos
            DB::table('role_has_permissions')->delete();
            DB::table('permissions')->delete();

            // Permisos globales
            foreach ($config['global_permissions'] as $perm) {
                Permission::create(['name' => $perm])
                    ->syncRoles([$roles['super_root'], $roles['Administrador']]);
            }

            // CRUDs
            foreach ($config['views'] as $view) {
                foreach (['index', 'create', 'edit', 'show', 'destroy'] as $action) {

                    $p = Permission::create(['name' => "$view.$action"]);

                    $assignTo = array_map(
                        fn($r) => $roles[$r],
                        $config['crud_default_roles']
                    );

                    $p->syncRoles($assignTo);
                }
            }
        });

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info('¡Permisos sincronizados desde config/permissions.php!');
    }

}