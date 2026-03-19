<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // ✅ Origen exacto del frontend — NO usar '*' cuando supports_credentials=true
    // http://localhost:8000 / http://127.0.0.1:8000 → Swagger UI (mismo servidor Laravel)
    'allowed_origins' => ['http://localhost:3000', 'http://localhost:8000', 'http://127.0.0.1:8000', 'https://learn-laravel-nine.vercel.app'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // ✅ Obligatorio para que las cookies (refresh_token httpOnly) funcionen
    'supports_credentials' => true,

];
