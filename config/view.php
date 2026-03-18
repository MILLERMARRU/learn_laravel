<?php

return [

    'paths' => [
        resource_path('views'),
    ],

    // En entornos serverless (Vercel) realpath() retorna false si el directorio
    // no existe en el filesystem read-only, por eso se usa VIEW_COMPILED_PATH.
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        storage_path('framework/views')
    ),

];
