<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Verifica que el usuario autenticado tenga uno de los roles indicados.
     *
     * Uso en rutas: ->middleware('role:Administrador')
     *               ->middleware('role:Administrador,Vendedor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = auth('api')->user();

        if (! $usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.',
            ], 401);
        }

        $rolUsuario = $usuario->rol?->nombre;

        if (! in_array($rolUsuario, $roles, strict: true)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.',
            ], 403);
        }

        return $next($request);
    }
}
