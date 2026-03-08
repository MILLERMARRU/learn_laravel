<?php

namespace App\Services\Contracts;

use App\Models\Rol;
use Illuminate\Pagination\LengthAwarePaginator;

interface RolServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Rol;

    public function crear(array $data): Rol;

    public function actualizar(Rol $rol, array $data): Rol;

    public function eliminar(Rol $rol): bool;
}
