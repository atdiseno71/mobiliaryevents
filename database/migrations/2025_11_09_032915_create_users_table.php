<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('codigo')->nullable();
            $table->string('tipo_identificacion')->nullable();
            $table->string('identificacion')->nullable();
            $table->string('name'); // Nombre del usuario
            $table->string('email')->unique()->nullable();

            // Datos de ubicación
            $table->foreignId('pais_id')->constrained('paises')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('ciudad_id')->constrained('ciudades')->cascadeOnDelete();
            $table->string('direccion')->nullable();

            // Contacto
            $table->string('telefono_fijo')->nullable();
            $table->string('telefono_movil')->nullable();

            // Otros campos
            $table->unsignedBigInteger('nivel')->nullable()->constrained()->onDelete('cascade');
            $table->foreign('nivel')
                ->references('id')
                ->on('roles');

            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->string('foto')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->constrained()->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')
                ->on('users');

            // Seguridad
            $table->string('password');
            $table->rememberToken();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
