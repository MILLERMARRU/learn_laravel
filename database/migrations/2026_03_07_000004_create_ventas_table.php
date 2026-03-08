<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('almacen_id')->constrained('almacenes')->restrictOnDelete();
            $table->date('fecha');
            $table->string('cliente');
            $table->decimal('total', 10, 2);
            $table->string('numero_comprobante')->unique();
            $table->string('tipo_pago');        // efectivo, tarjeta, transferencia, otro
            $table->string('estado');           // pendiente, completada, cancelada
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
