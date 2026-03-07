<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('direccion');
            $table->string('responsable');
            $table->string('telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->softDeletes();              // deleted_at — preserva FK en ventas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacenes');
    }
};
