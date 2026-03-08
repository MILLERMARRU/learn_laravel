<?php

namespace App\Repositories\Contracts;

use App\Models\Inventario;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventarioRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Inventario;

    public function create(array $data): Inventario;

    public function update(Inventario $inventario, array $data): Inventario;

    public function findByProductoAlmacen(int $productoId, int $almacenId): ?Inventario;

    /** Hard delete — no tiene historial propio ni es FK en otras tablas */
    public function delete(Inventario $inventario): bool;
}
