<?php

namespace App\Repositories\Contracts;

use App\Models\Almacen;
use Illuminate\Pagination\LengthAwarePaginator;

interface AlmacenRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Almacen;

    public function create(array $data): Almacen;

    public function update(Almacen $almacen, array $data): Almacen;

    /** Soft delete */
    public function delete(Almacen $almacen): bool;
}
