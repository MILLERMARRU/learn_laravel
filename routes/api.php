<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\InventarioController;
use App\Http\Controllers\Api\MovimientoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

/*
|──────────────────────────────────────────────────────────────
| API Routes — v1
|──────────────────────────────────────────────────────────────
| Prefijo base: /api  (definido en bootstrap/app.php)
| Prefijo aquí: /v1
|
| Nota: las rutas de Auth y el middleware JWT se añadirán
|       en la siguiente iteración.
*/

Route::prefix('v1')->group(function () {

    // ── Categorías ───────────────────────────────────────────
    Route::apiResource('categorias', CategoriaController::class);

    // ── Productos ─────────────────────────────────────────────
    Route::apiResource('productos', ProductoController::class);

    // ── Almacenes ─────────────────────────────────────────────
    // parameters() fuerza {almacen} en lugar de {almacene} (plural mal singularizado)
    Route::apiResource('almacenes', AlmacenController::class)
        ->parameters(['almacenes' => 'almacen']);

    // ── Inventario ────────────────────────────────────────────
    Route::apiResource('inventario', InventarioController::class);

    // ── Roles ─────────────────────────────────────────────────
    // parameters() fuerza {rol} en lugar de {role} (inflector inglés incorrecto)
    Route::apiResource('roles', RolController::class)
        ->parameters(['roles' => 'rol']);

    // ── Usuarios ──────────────────────────────────────────────
    Route::apiResource('usuarios', UsuarioController::class);

    // ── Ventas ────────────────────────────────────────────────
    Route::apiResource('ventas', VentaController::class);

    // ── Movimientos ───────────────────────────────────────────
    // Solo index, store y show — tabla de auditoría, no se edita ni elimina
    Route::apiResource('movimientos', MovimientoController::class)
        ->only(['index', 'store', 'show']);

});
