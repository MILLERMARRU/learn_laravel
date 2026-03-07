<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ProductoController;
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

});
