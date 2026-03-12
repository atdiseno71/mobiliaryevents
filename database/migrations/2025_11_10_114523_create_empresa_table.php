<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 20)->unique();
            $table->string('nombre', 100);
            $table->string('email', 100)->nullable();
            $table->string('pagina_web', 150)->nullable();
            $table->string('pais', 50)->default('Colombia');
            $table->string('region', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('telefonos', 100)->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });

        // Insertar el registro inicial de la empresa
        DB::table('empresa')->insert([
            'nit' => '900.723.262-0',
            'nombre' => 'FIERRO PRODUCCIONES',
            'email' => 'fierroproducciones@gmail.com',
            'pagina_web' => 'www.fierroproducciones.com',
            'pais' => 'Colombia',
            'region' => 'Valle del Cauca',
            'ciudad' => 'Palmira',
            'direccion' => 'Calle 29 19-21',
            'telefonos' => '315 5661002 - 317 4414930',
            'logo' => 'img/logofierrofnegro_2.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
