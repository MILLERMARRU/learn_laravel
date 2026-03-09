<?php

namespace App\Services\Contracts;

use App\Models\Usuario;

interface AuthServiceInterface
{
    public function login(array $credentials): array;

    public function refresh(string $refreshToken): array;

    public function logout(string $refreshToken): void;

    public function me(): Usuario;
}
