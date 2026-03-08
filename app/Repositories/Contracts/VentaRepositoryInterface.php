<?php

namespace App\Repositories\Contracts;

use App\Models\Venta;
use Illuminate\Pagination\LengthAwarePaginator;

interface VentaRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Venta;

    public function create(array $data): Venta;

    public function update(Venta $venta, array $data): Venta;

    /** Soft delete */
    public function delete(Venta $venta): bool;
}
