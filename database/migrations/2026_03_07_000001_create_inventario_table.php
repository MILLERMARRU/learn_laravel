<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(0);
            $table->unsignedInteger('cantidad_reservada')->default(0);
            $table->unsignedInteger('cantidad_minima')->default(0);
            $table->timestamp('ultima_actualizacion')->useCurrent();
            $table->timestamps();

            // Un producto solo puede tener un registro por almacén
            $table->unique(['producto_id', 'almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
