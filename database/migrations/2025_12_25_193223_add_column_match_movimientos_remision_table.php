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
        Schema::table('movimientos_remision', function (Blueprint $table) {
            // Auditoría
            $table->unsignedBigInteger('estado_id')->default(1)->nullable()->after('created_by');
            $table->foreign('estado_id')
                ->references('id')
                ->on('estados')
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
        Schema::table('movimientos_remision', function (Blueprint $table) {
            //
        });
    }
};
