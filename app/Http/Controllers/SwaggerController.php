<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SwaggerController extends Controller
{
    /**
     * Sirve la interfaz Swagger UI (HTML con assets via CDN).
     * Solo accesible en entornos no productivos.
     */
    public function ui(): Response
    {
        $specUrl = url('/docs/spec');

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>API Docs — Inventario</title>
            <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
            <style>
                body { margin: 0; }
                .swagger-ui .topbar { background-color: #1b1b1b; }
                .swagger-ui .topbar .download-url-wrapper { display: none; }
            </style>
        </head>
        <body>
            <div id="swagger-ui"></div>
            <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
            <script>
                SwaggerUIBundle({
                    url: '{$specUrl}',
                    dom_id: '#swagger-ui',
                    presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
                    layout: 'BaseLayout',
                    deepLinking: true,
                    tryItOutEnabled: true,
                });
            </script>
        </body>
        </html>
        HTML;

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    /**
     * Sirve el archivo openapi.yaml como texto plano.
     * Swagger UI lo consume para renderizar la documentación.
     */
    public function spec(): Response
    {
        $path = base_path('docs/openapi.yaml');

        abort_unless(file_exists($path), 404, 'Spec no encontrada.');

        return response(file_get_contents($path), 200)
            ->header('Content-Type', 'application/yaml');
    }
}
