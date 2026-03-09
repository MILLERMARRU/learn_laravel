<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias para el middleware de roles — uso: ->middleware('role:Administrador')
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Encriptar cookies (necesario para leer la cookie httpOnly del refresh token)
        $middleware->encryptCookies(except: ['refresh_token']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── Errores de validación → formato apiResponse estándar ──
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los datos enviados no son válidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // ── JWT expirado ──────────────────────────────────────────
        $exceptions->render(function (TokenExpiredException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El token ha expirado. Por favor renuévalo en /auth/refresh.',
                ], 401);
            }
        });

        // ── JWT inválido ──────────────────────────────────────────
        $exceptions->render(function (TokenInvalidException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El token no es válido.',
                ], 401);
            }
        });

        // ── JWT ausente ───────────────────────────────────────────
        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no encontrado. Por favor inicia sesión.',
                ], 401);
            }
        });

    })->create();
