<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UsuarioResource;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Nombre de la cookie del refresh token
    private const COOKIE_NAME = 'refresh_token';

    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    /**
     * POST /api/v1/auth/login
     * Autentica al usuario y retorna access token.
     * El refresh token se envía en una cookie httpOnly.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $resultado = $this->authService->login($request->validated());
        } catch (\RuntimeException $e) {
            return $this->apiResponse(
                success: false,
                message: $e->getMessage(),
                status: 401,
            );
        }

        $cookie = $this->buildRefreshCookie($resultado['refresh_token']);

        return $this->apiResponse(
            success: true,
            message: 'Sesión iniciada correctamente.',
            data: [
                'access_token' => $resultado['access_token'],
                'token_type'   => $resultado['token_type'],
                'expires_in'   => $resultado['expires_in'],
                'usuario'      => new UsuarioResource($resultado['usuario']),
            ],
        )->withCookie($cookie);
    }

    /**
     * POST /api/v1/auth/refresh
     * Rota el refresh token y emite un nuevo access token.
     * Lee el refresh token de la cookie httpOnly automáticamente.
     */
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie(self::COOKIE_NAME);

        if (! $refreshToken) {
            return $this->apiResponse(
                success: false,
                message: 'Refresh token no encontrado. Por favor inicia sesión.',
                status: 401,
            );
        }

        try {
            $resultado = $this->authService->refresh($refreshToken);
        } catch (\RuntimeException $e) {
            // Limpiar cookie inválida
            $cookie = cookie()->forget(self::COOKIE_NAME);

            return $this->apiResponse(
                success: false,
                message: $e->getMessage(),
                status: 401,
            )->withCookie($cookie);
        }

        $nuevaCookie = $this->buildRefreshCookie($resultado['refresh_token']);

        return $this->apiResponse(
            success: true,
            message: 'Token renovado correctamente.',
            data: [
                'access_token' => $resultado['access_token'],
                'token_type'   => $resultado['token_type'],
                'expires_in'   => $resultado['expires_in'],
                'usuario'      => new UsuarioResource($resultado['usuario']),
            ],
        )->withCookie($nuevaCookie);
    }

    /**
     * POST /api/v1/auth/logout
     * Invalida el access token y elimina el refresh token.
     * Requiere auth:api.
     */
    public function logout(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie(self::COOKIE_NAME) ?? '';

        $this->authService->logout($refreshToken);

        // Borrar la cookie del navegador
        $cookie = cookie()->forget(self::COOKIE_NAME);

        return $this->apiResponse(
            success: true,
            message: 'Sesión cerrada correctamente.',
        )->withCookie($cookie);
    }

    /**
     * GET /api/v1/auth/me
     * Retorna el usuario autenticado con su rol.
     * Requiere auth:api.
     */
    public function me(): JsonResponse
    {
        $usuario = $this->authService->me();

        return $this->apiResponse(
            success: true,
            message: 'Usuario autenticado.',
            data: new UsuarioResource($usuario),
        );
    }

    // ── Helper ───────────────────────────────────────────────────

    /**
     * Construye la cookie httpOnly segura del refresh token.
     */
    private function buildRefreshCookie(string $token): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie(
            name:     self::COOKIE_NAME,
            value:    $token,
            minutes:  (int) env('REFRESH_TOKEN_TTL', 1440),
            path:     '/api/v1/auth',     // solo se envía a endpoints de auth
            secure:   app()->isProduction(), // HTTPS en producción
            httpOnly: true,                  // JS no puede leerla
            sameSite: 'Strict',              // protección CSRF
        );
    }
}
