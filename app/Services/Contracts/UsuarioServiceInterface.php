<?php

namespace App\Services\Contracts;

use App\Models\Usuario;
use Illuminate\Pagination\LengthAwarePaginator;

interface UsuarioServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Usuario;

    public function crear(array $data): Usuario;

    public function actualizar(Usuario $usuario, array $data): Usuario;

    public function eliminar(Usuario $usuario): bool;
}
