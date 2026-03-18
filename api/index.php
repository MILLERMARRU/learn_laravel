<?php

echo "PHP OK - step 1\n";

$storagePath = '/tmp/laravel-storage';
@mkdir($storagePath . '/framework/cache/data', 0777, true);
@mkdir($storagePath . '/framework/sessions', 0777, true);
@mkdir($storagePath . '/framework/views', 0777, true);
@mkdir($storagePath . '/logs', 0777, true);
putenv('VERCEL_STORAGE_PATH=' . $storagePath);

echo "PHP OK - step 2 (storage dirs created)\n";

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die("FATAL: vendor/autoload.php not found\n");
}

echo "PHP OK - step 3 (vendor found)\n";

require __DIR__ . '/../public/index.php';
