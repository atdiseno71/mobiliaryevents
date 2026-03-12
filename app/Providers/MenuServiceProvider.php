<?php

namespace App\Providers;

use App\Models\Grupo;
use App\Models\Categoria;
use App\Models\SubCategoria;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function boot()
    {

        // Evitar que el provider cargue modelos durante `composer install`, `artisan`, etc
        if ($this->app->runningInConsole()) {
            return;
        }

        // Cargar menú base desde config/menu.php
        $menu = include base_path('config/menu.php');

        // Verificar tablas una sola vez
        $hasGrupos = Schema::hasTable('grupos');
        $hasCategorias = Schema::hasTable('categorias');
        $hasSubcategorias = Schema::hasTable('subcategorias');
        $hasSubreferencias = Schema::hasTable('subreferencias');

        // Verificar existencia de registros solo si las tablas existen
        $existenGrupos = $hasGrupos && Grupo::exists();
        $existenCategorias = $hasCategorias && Categoria::exists();
        $existenSubCategorias = $hasSubreferencias && SubCategoria::exists();

        foreach ($menu as &$section) {
            if ($section['text'] !== 'Parámetros') {
                continue;
            }

            $section['submenu'] = array_filter($section['submenu'], function ($item) use ($hasGrupos, $hasCategorias, $hasSubcategorias, $hasSubreferencias, $existenGrupos, $existenCategorias, $existenSubCategorias) {
                return match ($item['text']) {
                    'Categorias' => $hasGrupos && $existenGrupos,
                    'SubCategorias' => $hasSubcategorias && $existenCategorias,
                    'SubReferencias' => $hasSubreferencias && $existenSubCategorias,
                    default => true,
                };
            });
        }

        // Cargar menú actualizado en configuración de AdminLTE
        config(['adminlte.menu' => $menu]);
    }
}
