<?php

namespace App\Services\Contracts;

use App\Models\Producto;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductoServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Producto;

    public function crear(array $data): Producto;

    public function actualizar(Producto $producto, array $data): Producto;

    /** Soft delete */
    public function eliminar(Producto $producto): bool;
}
