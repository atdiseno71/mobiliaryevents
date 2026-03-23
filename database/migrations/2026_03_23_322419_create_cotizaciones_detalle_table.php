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
        Schema::create('cotizaciones_detalle', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cotizacion_id');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->unsignedBigInteger('combinacion_id')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('cantidad', 30)->default('1');

            $table->foreign('cotizacion_id')
                ->references('id')
                ->on('cotizaciones')
                ->onDelete('cascade');

            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->onDelete('cascade');

            $table->foreign('combinacion_id')
                ->references('id')
                ->on('combinaciones')
                ->onDelete('cascade');

            $table->foreign('referencia_id')
                ->references('id')
                ->on('subreferencias')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones_detalle');
    }
};