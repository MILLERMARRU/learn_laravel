<?php

namespace App\Services\Contracts;

use App\Models\Venta;
use Illuminate\Pagination\LengthAwarePaginator;

interface VentaServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Venta;

    public function crear(array $data): Venta;

    public function actualizar(Venta $venta, array $data): Venta;

    public function eliminar(Venta $venta): bool;
}
