<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('cod_producto')->unique();
            $table->string('nombre');
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('marca')->nullable();
            $table->string('unidad_medida');           // kg, lt, unidad, caja, etc.
            $table->string('contenido')->nullable();    // "500ml", "1kg", "12 unidades"
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_minorista', 10, 2);
            $table->decimal('precio_mayorista', 10, 2);
            $table->unsignedInteger('stock_minimo')->default(0);
            $table->boolean('activo')->default(true);
            $table->softDeletes();                     // deleted_at — soft delete
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
