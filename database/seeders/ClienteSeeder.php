<?php

namespace Database\Seeders;

use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener IDs de ubicaciones (asumiendo que ya existen en la base de datos)
        $paisColombia = Pais::where('nombre', 'Colombia')->first();
        $departamentoValle = Departamento::where('nombre', 'Valle del Cauca')->first();
        $ciudadPalmira = Ciudad::where('nombre', 'Palmira')->first();

        Cliente::create([
            'nombre' => '---',
            'email' => null,
            'telefonos' => '---',
            'direccion' => '---',
            'pais_id' => $paisColombia ? $paisColombia->id : 1,
            'departamento_id' => $departamentoValle ? $departamentoValle->id : 1,
            'ciudad_id' => $ciudadPalmira ? $ciudadPalmira->id : 1,
        ]);
    }
}
