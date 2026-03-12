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
        Schema::table('remisiones_detalle', function (Blueprint $table) {
            $table->unsignedBigInteger('combinacion_id')->nullable()->after('producto_id');

            $table->foreign('combinacion_id')
                ->references('id')->on('combinaciones')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('remisiones_detalle', function (Blueprint $table) {
            //
        });
    }
};
