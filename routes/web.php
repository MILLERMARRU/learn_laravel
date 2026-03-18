<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── API Docs ──────────────────────────────────────────────────
Route::get('/docs', [SwaggerController::class, 'ui']);
Route::get('/docs/spec', [SwaggerController::class, 'spec']);
