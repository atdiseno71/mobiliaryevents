<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();

            // Ubicación
            $table->unsignedBigInteger('ciudad_id')->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('telefono', 30)->nullable();

            // Coordenadas opcionales
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Responsable del almacén
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->foreign('responsable_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            // Estado (activo/inactivo)
            $table->boolean('activo')->default(true);

            // Relaciones
            $table->foreign('ciudad_id')
                ->references('id')->on('ciudades')
                ->onDelete('set null');

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('almacenes');
    }
};
