<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("ALTER TABLE movimientos_remision MODIFY motivo LONGTEXT NULL");
        DB::statement("
            ALTER TABLE movimientos_inventario
            MODIFY motivo LONGTEXT NULL
        ");
    }

    public function down()
    {
        DB::statement("ALTER TABLE movimientos_remision MODIFY motivo LONGTEXT NOT NULL");
        DB::statement("
            ALTER TABLE movimientos_inventario
            MODIFY motivo VARCHAR(255) NOT NULL
        ");
    }
};
