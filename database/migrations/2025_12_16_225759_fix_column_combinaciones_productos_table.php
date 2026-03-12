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
        Schema::table('combinaciones_productos', function (Blueprint $table) {
            $table->unique('producto_id');
            $table->index('combinacion_id');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('combinaciones_productos', function (Blueprint $table) {
            $table->dropIndex('combinacion_id');
            $table->dropIndex('producto_id');
            $table->dropUnique('producto_id');
        });
    }
};
