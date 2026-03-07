<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── API Docs (solo en entorno local) ──────────────────────────
if (App::environment('local')) {
    Route::get('/docs', [SwaggerController::class, 'ui']);
    Route::get('/docs/spec', [SwaggerController::class, 'spec']);
}
