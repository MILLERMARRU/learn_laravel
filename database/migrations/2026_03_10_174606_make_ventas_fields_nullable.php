<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente')->nullable()->change();
            $table->string('numero_comprobante')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente')->nullable(false)->default('')->change();
            $table->string('numero_comprobante')->nullable(false)->default('')->change();
        });
    }
};
