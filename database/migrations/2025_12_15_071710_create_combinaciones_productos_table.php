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
        Schema::create('combinaciones_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('combinacion_id');
            $table->unsignedBigInteger('producto_id');
            $table->timestamps();

            $table->foreign('combinacion_id')->references('id')->on('combinaciones')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combinaciones_productos');
    }
};
