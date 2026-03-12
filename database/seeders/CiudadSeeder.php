<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class CiudadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener departamentos
        $valle = Departamento::where('nombre', 'Valle del Cauca')->first();
        $bogota = Departamento::where('nombre', 'Bogotá D.C.')->first();
        $antioquia = Departamento::where('nombre', 'Antioquia')->first();
        $cundinamarca = Departamento::where('nombre', 'Cundinamarca')->first();
        $santander = Departamento::where('nombre', 'Santander')->first();

        if (!$valle) {
            $this->command->error('Primero debes ejecutar el seeder de departamentos.');
            return;
        }

        $ciudades = [
            // Valle del Cauca
            ['nombre' => 'Palmira', 'departamento_id' => $valle->id],
            ['nombre' => 'Cali', 'departamento_id' => $valle->id],
            ['nombre' => 'Buenaventura', 'departamento_id' => $valle->id],
            ['nombre' => 'Buga', 'departamento_id' => $valle->id],
            ['nombre' => 'Cartago', 'departamento_id' => $valle->id],
            ['nombre' => 'Tuluá', 'departamento_id' => $valle->id],
            ['nombre' => 'Yumbo', 'departamento_id' => $valle->id],
            ['nombre' => 'Jamundí', 'departamento_id' => $valle->id],
            ['nombre' => 'Florida', 'departamento_id' => $valle->id],
            ['nombre' => 'Pradera', 'departamento_id' => $valle->id],

            // Bogotá
            ['nombre' => 'Bogotá', 'departamento_id' => $bogota->id],

            // Antioquia
            ['nombre' => 'Medellín', 'departamento_id' => $antioquia->id],
            ['nombre' => 'Bello', 'departamento_id' => $antioquia->id],
            ['nombre' => 'Itagüí', 'departamento_id' => $antioquia->id],
            ['nombre' => 'Envigado', 'departamento_id' => $antioquia->id],

            // Cundinamarca
            ['nombre' => 'Soacha', 'departamento_id' => $cundinamarca->id],
            ['nombre' => 'Fusagasugá', 'departamento_id' => $cundinamarca->id],
            ['nombre' => 'Girardot', 'departamento_id' => $cundinamarca->id],
            ['nombre' => 'Facatativá', 'departamento_id' => $cundinamarca->id],

            // Santander
            ['nombre' => 'Bucaramanga', 'departamento_id' => $santander->id],
            ['nombre' => 'Floridablanca', 'departamento_id' => $santander->id],
            ['nombre' => 'Girón', 'departamento_id' => $santander->id],
            ['nombre' => 'Piedecuesta', 'departamento_id' => $santander->id],
        ];

        foreach ($ciudades as $ciudad) {
            Ciudad::create($ciudad);
        }

        $this->command->info('Ciudades creadas exitosamente!');
    }
}