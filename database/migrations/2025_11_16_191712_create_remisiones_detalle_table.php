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
        Schema::create('remisiones_detalle', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('remision_id');
            $table->foreign('remision_id')
                ->references('id')
                ->on('remisiones')
                ->onDelete('cascade');

            $table->unsignedBigInteger('producto_id')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();

            $table->foreign('producto_id')
                ->references('id')->on('productos')
                ->onDelete('cascade');

            $table->foreign('referencia_id')
                ->references('id')->on('subreferencias')
                ->onDelete('cascade'); // Apunta también a productos (el padre)

            $table->integer('cantidad')->default(1);

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
        Schema::dropIfExists('remisiones_detalle');
    }
};
