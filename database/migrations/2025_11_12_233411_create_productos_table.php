<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            // Relaciones jerárquicas
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->cascadeOnDelete();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->cascadeOnDelete();

            // Datos básicos
            $table->string('referencia')->nullable(); // modelo
            $table->boolean('inventario_por_serie')->default(false);
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Códigos
            $table->string('codigo_interno')->unique();
            $table->string('codigo_qr')->nullable()->unique();

            // Valores
            $table->decimal('valor_compra', 15, 2)->nullable()->default(0);
            $table->decimal('valor_alquiler', 15, 2)->nullable()->default(0);

            // Imagen
            $table->string('imagen')->nullable();

            // Clasificación y estado
            $table->enum('clase', ['Alquiler', 'Insumo'])->default('Insumo');
            $table->decimal('peso_unitario', 10, 2)->nullable();
            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['grupo_id', 'categoria_id', 'subcategoria_id']);
            $table->index('codigo_interno');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
