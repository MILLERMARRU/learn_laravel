<?php

namespace App\Repositories\Contracts;

use App\Models\DetalleVenta;
use Illuminate\Support\Collection;

interface DetalleVentaRepositoryInterface
{
    public function allByVenta(int $ventaId): Collection;

    public function find(int $id): ?DetalleVenta;

    public function create(array $data): DetalleVenta;

    public function sumarTotalVenta(int $ventaId): float;

    public function delete(DetalleVenta $detalle): bool;
}
