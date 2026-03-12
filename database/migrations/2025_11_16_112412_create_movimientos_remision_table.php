<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('movimientos_remision', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('almacen_id');
            $table->unsignedBigInteger('remision_id');

            $table->enum('tipo', ['ingreso', 'salida']);
            $table->text('motivo')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('almacen_id')->references('id')->on('almacenes')->onDelete('cascade');
            $table->foreign('remision_id')->references('id')->on('remisiones')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });


    }

    public function down()
    {
        Schema::dropIfExists('movimientos_remision');
    }
};
