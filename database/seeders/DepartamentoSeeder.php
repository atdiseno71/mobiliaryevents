<?php

namespace Database\Seeders;

use App\Models\Pais;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener Colombia
        $colombia = Pais::where('nombre', 'Colombia')->first();

        if (!$colombia) {
            $this->command->error('Primero debes ejecutar el seeder de países.');
            return;
        }

        $departamentos = [
            ['nombre' => 'Valle del Cauca'],
            ['nombre' => 'Bogotá D.C.'],
            ['nombre' => 'Antioquia'],
            ['nombre' => 'Cundinamarca'],
            ['nombre' => 'Santander'],
            ['nombre' => 'Atlántico'],
            ['nombre' => 'Bolívar'],
            ['nombre' => 'Boyacá'],
            ['nombre' => 'Caldas'],
            ['nombre' => 'Caquetá'],
            ['nombre' => 'Cauca'],
            ['nombre' => 'Cesar'],
            ['nombre' => 'Córdoba'],
            ['nombre' => 'Huila'],
            ['nombre' => 'La Guajira'],
            ['nombre' => 'Magdalena'],
            ['nombre' => 'Meta'],
            ['nombre' => 'Nariño'],
            ['nombre' => 'Norte de Santander'],
            ['nombre' => 'Quindío'],
            ['nombre' => 'Risaralda'],
            ['nombre' => 'San Andrés y Providencia'],
            ['nombre' => 'Sucre'],
            ['nombre' => 'Tolima'],
            ['nombre' => 'Arauca'],
            ['nombre' => 'Casanare'],
            ['nombre' => 'Putumayo'],
            ['nombre' => 'Amazonas'],
            ['nombre' => 'Guainía'],
            ['nombre' => 'Guaviare'],
            ['nombre' => 'Vaupés'],
            ['nombre' => 'Vichada']
        ];

        foreach ($departamentos as $departamento) {
            Departamento::create([
                'nombre' => $departamento['nombre'],
                'pais_id' => $colombia->id
            ]);
        }

        $this->command->info('Departamentos de Colombia creados exitosamente!');
    }
}