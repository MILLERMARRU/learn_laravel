<?php

$storagePath = '/tmp/laravel-storage';
@mkdir($storagePath . '/framework/cache/data', 0777, true);
@mkdir($storagePath . '/framework/sessions', 0777, true);
@mkdir($storagePath . '/framework/views', 0777, true);
@mkdir($storagePath . '/logs', 0777, true);
putenv('VERCEL_STORAGE_PATH=' . $storagePath);

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "=== LARAVEL ERROR ===\n";
    echo $e->getMessage() . "\n\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
}
