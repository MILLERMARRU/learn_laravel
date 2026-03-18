<?php

// En Vercel el filesystem es read-only — redirigir storage a /tmp
$storagePath = '/tmp/laravel-storage';
@mkdir($storagePath . '/framework/cache/data', 0777, true);
@mkdir($storagePath . '/framework/sessions', 0777, true);
@mkdir($storagePath . '/framework/views', 0777, true);
@mkdir($storagePath . '/logs', 0777, true);
putenv('VERCEL_STORAGE_PATH=' . $storagePath);

require __DIR__ . '/../public/index.php';
