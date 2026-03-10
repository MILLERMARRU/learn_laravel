<?php

namespace App\Repositories;

use App\Models\DetalleVenta;
use App\Repositories\Contracts\DetalleVentaRepositoryInterface;
use Illuminate\Support\Collection;

class DetalleVentaRepository implements DetalleVentaRepositoryInterface
{
    public function allByVenta(int $ventaId): Collection
    {
        return DetalleVenta::with(['producto', 'almacen', 'movimiento'])
            ->where('venta_id', $ventaId)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function find(int $id): ?DetalleVenta
    {
        return DetalleVenta::with(['producto', 'almacen', 'movimiento'])->find($id);
    }

    public function create(array $data): DetalleVenta
    {
        return DetalleVenta::create($data);
    }

    public function sumarTotalVenta(int $ventaId): float
    {
        return (float) DetalleVenta::where('venta_id', $ventaId)->sum('sub_total');
    }

    public function delete(DetalleVenta $detalle): bool
    {
        return $detalle->forceDelete();
    }
}
