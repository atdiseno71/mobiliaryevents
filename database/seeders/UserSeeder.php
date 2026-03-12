<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Departamento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Obtener ubicaciones por defecto (Colombia, Valle del Cauca, Palmira)
        $paisColombia = Pais::where('nombre', 'Colombia')->first();
        $departamentoValle = Departamento::where('nombre', 'Valle del Cauca')->first();
        $ciudadPalmira = Ciudad::where('nombre', 'Palmira')->first();

        // Usuario 1 - Jorge Usuga
        User::create([
            'codigo' => 'jusuga',
            'tipo_identificacion' => 'Cédula de Ciudadanía',
            'identificacion' => '123456789',
            'name' => 'Jorge',
            'email' => 'jorge.usuga@empresa.com',
            
            // Ubicación
            'pais_id' => $paisColombia ? $paisColombia->id : 1,
            'departamento_id' => $departamentoValle ? $departamentoValle->id : 1,
            'ciudad_id' => $ciudadPalmira ? $ciudadPalmira->id : 1,
            'direccion' => 'Calle 25 # 15-30, Barrio Centro',
            
            // Contacto
            'telefono_fijo' => '6021234567',
            'telefono_movil' => '3001234567',
            
            // Otros campos
            'nivel' => 1,
            'estado' => 'Activo',
            'foto' => null,
            
            // Seguridad
            'password' => Hash::make('12345678'),
        ])->assignRole('super_root');

        // Usuario 2 - Alejandro Gallego
        User::create([
            'codigo' => 'agallego',
            'tipo_identificacion' => 'Cédula de Ciudadanía',
            'identificacion' => '987654321',
            'name' => 'Alejo',
            'email' => 'alejandro.gallego@empresa.com',
            
            // Ubicación
            'pais_id' => $paisColombia ? $paisColombia->id : 1,
            'departamento_id' => $departamentoValle ? $departamentoValle->id : 1,
            'ciudad_id' => $ciudadPalmira ? $ciudadPalmira->id : 1,
            'direccion' => 'Avenida 6N # 23-45, Urbanización Los Almendros',
            
            // Contacto
            'telefono_fijo' => '6027654321',
            'telefono_movil' => '3109876543',
            
            // Otros campos
            'nivel' => 1,
            'estado' => 'Activo',
            'foto' => null,
            
            // Seguridad
            'password' => Hash::make('12345678'),
        ])->assignRole('super_root');
    }
}
