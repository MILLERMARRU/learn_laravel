<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Debug temporal — eliminar después ─────────────────────────
Route::get('/debug-uri', function () {
    return response()->json([
        'REQUEST_URI'  => $_SERVER['REQUEST_URI']  ?? null,
        'PATH_INFO'    => $_SERVER['PATH_INFO']    ?? null,
        'SCRIPT_NAME'  => $_SERVER['SCRIPT_NAME']  ?? null,
        'HTTP_HOST'    => $_SERVER['HTTP_HOST']    ?? null,
    ]);
});

// ── API Docs ──────────────────────────────────────────────────
Route::get('/docs', [SwaggerController::class, 'ui']);
Route::get('/docs/spec', [SwaggerController::class, 'spec']);
