<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['nombre', 'departamento_id']); // Evita duplicados por departamento
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};
