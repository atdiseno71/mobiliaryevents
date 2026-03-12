<?php

namespace App\Providers;

use App\Models\Grupo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Interceptar chequeos de permiso
        Gate::before(function ($user, $ability) {
            // Si están verificando acceso a categorías
            if (str_starts_with($ability, 'categorias.')) {
                // Si NO existen grupos, bloquear sin importar permisos
                if (!Grupo::exists()) {
                    return false;
                }
            }
        });
    }
}
