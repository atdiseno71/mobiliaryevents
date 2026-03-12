<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paises = [
            [
                'nombre' => 'Colombia',
                'codigo' => 'COL'
            ],
            [
                'nombre' => 'Estados Unidos',
                'codigo' => 'USA'
            ],
            [
                'nombre' => 'México',
                'codigo' => 'MEX'
            ],
            [
                'nombre' => 'España',
                'codigo' => 'ESP'
            ],
            [
                'nombre' => 'Argentina',
                'codigo' => 'ARG'
            ],
            [
                'nombre' => 'Chile',
                'codigo' => 'CHL'
            ],
            [
                'nombre' => 'Perú',
                'codigo' => 'PER'
            ],
            [
                'nombre' => 'Ecuador',
                'codigo' => 'ECU'
            ],
            [
                'nombre' => 'Brasil',
                'codigo' => 'BRA'
            ],
            [
                'nombre' => 'Venezuela',
                'codigo' => 'VEN'
            ]
        ];

        foreach ($paises as $pais) {
            Pais::create($pais);
        }

        $this->command->info('Países creados exitosamente!');
    }
}