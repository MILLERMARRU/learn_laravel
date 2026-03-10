<?php

namespace App\Services\Contracts;

use App\Models\DetalleVenta;
use App\Models\Venta;
use Illuminate\Support\Collection;

interface DetalleVentaServiceInterface
{
    public function listar(Venta $venta): Collection;

    public function obtener(int $id): ?DetalleVenta;

    public function registrar(Venta $venta, array $data): DetalleVenta;

    public function eliminar(Venta $venta, DetalleVenta $detalle): void;
}
