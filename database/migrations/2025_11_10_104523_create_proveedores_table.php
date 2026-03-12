<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            // Información personal
            $table->string('nombre');
            $table->string('email')->nullable();
            $table->string('telefonos', 40)->nullable(); // Para almacenar múltiples teléfonos

            // Dirección
            $table->string('direccion')->nullable();

            // Relaciones geográficas
            $table->foreignId('pais_id')->constrained('paises')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('ciudad_id')->constrained('ciudades')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Índices para mejor performance
            $table->index('email');
            $table->index('pais_id');
            $table->index('departamento_id');
            $table->index('ciudad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};