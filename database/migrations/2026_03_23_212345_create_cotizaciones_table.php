<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();

            $table->string('consecutivo', 90)->unique();
            $table->string('contacto', 90)->nullable();

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('tipo_evento_id')->nullable();
            $table->unsignedBigInteger('ciudad_id')->nullable();

            $table->string('lugar', 120)->nullable();
            $table->dateTime('fecha_evento')->nullable();
            $table->dateTime('fecha_montaje')->nullable();
            $table->string('personal_ids')->nullable();
            $table->string('transporte', 60)->nullable();
            $table->string('placa', 20)->nullable();
            $table->string('id_conductor', 30)->nullable();
            $table->string('origen', 120)->nullable();
            $table->string('destino', 120)->nullable();

            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->onDelete('cascade');

            $table->foreign('tipo_evento_id')
                ->references('id')
                ->on('tipo_eventos')
                ->onDelete('cascade');

            $table->foreign('ciudad_id')
                ->references('id')
                ->on('ciudades')
                ->onDelete('cascade');

            $table->foreign('estado_id')
                ->references('id')
                ->on('estados')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('cotizaciones');
    }
};