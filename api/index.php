<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Crear directorios en /tmp para entorno serverless (Vercel filesystem read-only)
$storagePath = '/tmp/laravel-storage';
@mkdir($storagePath . '/framework/cache/data', 0777, true);
@mkdir($storagePath . '/framework/sessions', 0777, true);
@mkdir($storagePath . '/framework/views', 0777, true);
@mkdir($storagePath . '/logs', 0777, true);

// Verificar modo mantenimiento
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

// Crear la aplicación y redirigir storage ANTES de handleRequest
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($storagePath);

$app->handleRequest(Request::capture());
