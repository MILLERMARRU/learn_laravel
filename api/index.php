<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

// Verificar que los archivos clave existen antes de cargar Laravel
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('ERROR: vendor/autoload.php no encontrado. Ruta: ' . __DIR__);
}

if (!file_exists(__DIR__ . '/../public/index.php')) {
    die('ERROR: public/index.php no encontrado.');
}

require __DIR__ . '/../public/index.php';
