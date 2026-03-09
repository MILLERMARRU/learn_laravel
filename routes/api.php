<?php

use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\DetalleVentaController;
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
| Prefijo base : /api  (definido en bootstrap/app.php)
| Prefijo aquí : /v1
|
| Grupos:
|   1. Auth públicas   — sin middleware
|   2. Auth protegidas — requieren access token válido
|   3. Catálogo lectura — autenticado (admin + vendedor)
|   4. Operativas ventas — autenticado (admin + vendedor)
|   5. Solo Administrador — catálogo escritura + gestión
*/

Route::prefix('v1')->group(function () {

    // ══ 1. AUTH PÚBLICAS ═════════════════════════════════════════
    // No requieren token — acceso libre

    Route::prefix('auth')->group(function () {

        Route::post('login',   [AuthController::class, 'login']);
        Route::post('refresh', [AuthController::class, 'refresh']);

        // Logout y me requieren token válido
        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',      [AuthController::class, 'me']);
        });
    });

    // ══ 2. RUTAS PROTEGIDAS (token requerido) ════════════════════

    Route::middleware('auth:api')->group(function () {

        // ── 3. CATÁLOGO — LECTURA (admin + vendedor) ─────────────
        // El vendedor necesita ver productos, stock y almacenes para operar

        Route::apiResource('categorias', CategoriaController::class)
            ->only(['index', 'show']);

        Route::apiResource('productos', ProductoController::class)
            ->only(['index', 'show']);

        Route::apiResource('almacenes', AlmacenController::class)
            ->only(['index', 'show'])
            ->parameters(['almacenes' => 'almacen']);

        Route::apiResource('inventario', InventarioController::class)
            ->only(['index', 'show']);

        Route::apiResource('movimientos', MovimientoController::class)
            ->only(['index', 'show']);

        // ── 4. OPERATIVAS VENTAS (admin + vendedor) ───────────────
        // Vendedor puede crear y completar ventas con sus detalles

        Route::apiResource('ventas', VentaController::class)
            ->only(['index', 'store', 'show', 'update']);

        Route::apiResource('ventas.detalles', DetalleVentaController::class)
            ->only(['index', 'store', 'show']);

        // ══ 5. SOLO ADMINISTRADOR ════════════════════════════════

        Route::middleware('role:Administrador')->group(function () {

            // ── Catálogo escritura ────────────────────────────────

            Route::apiResource('categorias', CategoriaController::class)
                ->only(['store', 'update', 'destroy']);

            Route::apiResource('productos', ProductoController::class)
                ->only(['store', 'update', 'destroy']);

            Route::apiResource('almacenes', AlmacenController::class)
                ->only(['store', 'update', 'destroy'])
                ->parameters(['almacenes' => 'almacen']);

            Route::apiResource('inventario', InventarioController::class)
                ->only(['store', 'update', 'destroy']);

            // Movimientos manuales de entrada (reposición de stock)
            Route::apiResource('movimientos', MovimientoController::class)
                ->only(['store']);

            // ── Gestión de roles y usuarios ───────────────────────

            Route::apiResource('roles', RolController::class)
                ->parameters(['roles' => 'rol']);

            Route::apiResource('usuarios', UsuarioController::class);

            // ── Cancelar y eliminar ventas ────────────────────────
            // Vendedor puede completar, solo admin puede cancelar/eliminar

            Route::delete('ventas/{venta}', [VentaController::class, 'destroy']);
        });
    });
});
