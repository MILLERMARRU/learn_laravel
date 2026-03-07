<?php

namespace App\Services\Contracts;

use App\Models\Almacen;
use Illuminate\Pagination\LengthAwarePaginator;

interface AlmacenServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Almacen;

    public function crear(array $data): Almacen;

    public function actualizar(Almacen $almacen, array $data): Almacen;

    /** Soft delete */
    public function eliminar(Almacen $almacen): bool;
}
