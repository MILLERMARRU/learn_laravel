<?php

namespace App\Repositories\Contracts;

use App\Models\Producto;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductoRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Producto;

    public function create(array $data): Producto;

    public function update(Producto $producto, array $data): Producto;

    /** Soft delete */
    public function delete(Producto $producto): bool;
}
