<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique('productos_codigo_interno_unique');
            $table->dropUnique('productos_codigo_qr_unique');

            $table->string('codigo_interno')->nullable()->change();
            $table->string('codigo_qr')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo_interno')->nullable(false)->change();
            $table->string('codigo_qr')->nullable(false)->change();

            $table->unique('codigo_interno', 'productos_codigo_interno_unique');
            $table->unique('codigo_qr', 'productos_codigo_qr_unique');
        });
    }
};