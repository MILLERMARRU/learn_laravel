<?php

// ── Directorios en /tmp para entorno serverless (Vercel filesystem read-only) ──
$storageBase = '/tmp/laravel-storage';
@mkdir($storageBase . '/framework/cache/data', 0777, true);
@mkdir($storageBase . '/framework/sessions', 0777, true);
@mkdir($storageBase . '/framework/views', 0777, true);
@mkdir($storageBase . '/logs', 0777, true);

// Laravel lee estas vars de entorno para los archivos de caché de bootstrap
$cacheBase = '/tmp/laravel-cache';
@mkdir($cacheBase, 0777, true);
$_ENV['APP_PACKAGES_CACHE'] = $cacheBase . '/packages.php';
$_ENV['APP_SERVICES_CACHE'] = $cacheBase . '/services.php';
$_ENV['APP_CONFIG_CACHE']   = $cacheBase . '/config.php';
$_ENV['APP_ROUTES_CACHE']   = $cacheBase . '/routes-v7.php';
$_ENV['APP_EVENTS_CACHE']   = $cacheBase . '/events.php';

// ── Vercel PHP runtime strips /api prefix from REQUEST_URI ──────────────────
// Function lives at api/index.php, so /api/v1/foo becomes /v1/foo.
// We restore it so Laravel's router finds the correct api routes.
if (isset($_SERVER['REQUEST_URI']) && !str_starts_with($_SERVER['REQUEST_URI'], '/api')) {
    $_SERVER['REQUEST_URI'] = '/api' . $_SERVER['REQUEST_URI'];
}

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($storageBase);
$app->handleRequest(\Illuminate\Http\Request::capture());
