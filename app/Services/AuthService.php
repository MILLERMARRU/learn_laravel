<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\Usuario;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthServiceInterface
{

    /**
     * Autentica al usuario y genera access token + refresh token.
     * Valida que el usuario exista, la contraseña sea correcta y esté activo.
     */
    public function login(array $credentials): array
    {
        $usuario = Usuario::with('rol')
            ->where('email', $credentials['email'])
            ->first();

        // Verificar existencia y contraseña
        if (! $usuario || ! Hash::check($credentials['password'], $usuario->password_hash)) {
            throw new \RuntimeException('Credenciales incorrectas.');
        }

        // Verificar que el usuario esté activo
        if (! $usuario->activo) {
            throw new \RuntimeException('Tu cuenta está desactivada. Contacta al administrador.');
        }

        // Generar access token JWT
        $accessToken = JWTAuth::fromUser($usuario);

        // Registrar último acceso
        $usuario->ultimo_acceso = now();
        $usuario->save();

        // Generar y persistir refresh token
        $refreshToken = $this->crearRefreshToken($usuario);

        return [
            'access_token'  => $accessToken,
            'token_type'    => 'bearer',
            'expires_in'    => config('jwt.ttl') * 60, // segundos
            'refresh_token' => $refreshToken,
            'usuario'       => $usuario,
        ];
    }

    /**
     * Valida el refresh token, lo rota y emite un nuevo access token.
     */
    public function refresh(string $refreshToken): array
    {
        $hash = hash('sha256', $refreshToken);

        $registro = RefreshToken::where('token', $hash)->first();

        if (! $registro) {
            throw new \RuntimeException('Refresh token inválido.');
        }

        if ($registro->estaVencido()) {
            $registro->delete();
            throw new \RuntimeException('Refresh token expirado. Por favor inicia sesión nuevamente.');
        }

        $usuario = Usuario::with('rol')->find($registro->usuario_id);

        if (! $usuario || ! $usuario->activo) {
            $registro->delete();
            throw new \RuntimeException('Usuario no encontrado o desactivado.');
        }

        // Eliminar el refresh token usado (rotación)
        $registro->delete();

        // Emitir nuevo access token
        $nuevoAccessToken = JWTAuth::fromUser($usuario);

        // Emitir nuevo refresh token (rotación completa)
        $nuevoRefreshToken = $this->crearRefreshToken($usuario);

        return [
            'access_token'  => $nuevoAccessToken,
            'token_type'    => 'bearer',
            'expires_in'    => config('jwt.ttl') * 60,
            'refresh_token' => $nuevoRefreshToken,
            'usuario'       => $usuario,
        ];
    }

    /**
     * Invalida el access token y elimina el refresh token.
     */
    public function logout(string $refreshToken): void
    {
        // Invalidar el access token en la blacklist de JWT
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Exception) {
            // Si el token ya expiró no es un error crítico
        }

        // Eliminar refresh token de la BD
        $hash = hash('sha256', $refreshToken);
        RefreshToken::where('token', $hash)->delete();
    }

    /**
     * Retorna el usuario autenticado con su rol.
     */
    public function me(): Usuario
    {
        /** @var Usuario $usuario */
        $usuario = auth('api')->user();

        return $usuario->load('rol');
    }

    // ── Helpers privados ─────────────────────────────────────────

    /**
     * Genera un refresh token aleatorio, lo persiste hasheado y retorna el valor en crudo.
     */
    private function crearRefreshToken(Usuario $usuario): string
    {
        // Generar token aleatorio criptográficamente seguro (64 chars hex = 256 bits)
        $tokenCrudo = bin2hex(random_bytes(32));
        $tokenHash  = hash('sha256', $tokenCrudo);

        RefreshToken::create([
            'usuario_id' => $usuario->id,
            'token'      => $tokenHash,
            'expires_at' => now()->addMinutes((int) env('REFRESH_TOKEN_TTL', 1440)),
        ]);

        return $tokenCrudo;
    }
}
