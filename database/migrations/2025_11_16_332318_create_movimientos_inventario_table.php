<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('inventario_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('almacen_id');
            $table->unsignedBigInteger('remision_id')->nullable();
            $table->unsignedBigInteger('movimientos_remision_id')->nullable();

            $table->enum('tipo', ['ingreso', 'salida']);
            $table->integer('cantidad');

            $table->string('motivo')->nullable();     // "Compra", "Ajuste", "Remisión", "Daño", etc.
            $table->unsignedBigInteger('referencia_id')->nullable(); // opcional: remisión, orden, etc.
            $table->string('referencia_tipo')->nullable(); // polymorphic-like

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('inventario_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('almacen_id')->references('id')->on('almacenes')->onDelete('cascade');
            $table->foreign('remision_id')->references('id')->on('remisiones')->onDelete('cascade');
            $table->foreign('movimientos_remision_id')->references('id')->on('movimientos_remision')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });


    }

    public function down()
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
