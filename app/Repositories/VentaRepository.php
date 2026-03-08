<?php

namespace App\Repositories;

use App\Models\Venta;
use App\Repositories\Contracts\VentaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class VentaRepository implements VentaRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Venta::with(['usuario', 'almacen']);

        // Filtro por cliente o número de comprobante
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('cliente', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('numero_comprobante', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if (isset($filters['almacen_id'])) {
            $query->where('almacen_id', $filters['almacen_id']);
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['tipo_pago'])) {
            $query->where('tipo_pago', $filters['tipo_pago']);
        }

        // Filtro por rango de fechas
        if (! empty($filters['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filters['fecha_desde']);
        }

        if (! empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filters['fecha_hasta']);
        }

        // Incluir eliminados solo si se solicita explícitamente
        if (! empty($filters['con_eliminados'])) {
            $query->withTrashed();
        }

        return $query->orderBy('id', 'asc')->paginate(15);
    }

    public function find(int $id): ?Venta
    {
        return Venta::with(['usuario', 'almacen'])->find($id);
    }

    public function create(array $data): Venta
    {
        return Venta::create($data);
    }

    public function update(Venta $venta, array $data): Venta
    {
        $venta->update($data);

        return $venta->fresh(['usuario', 'almacen']);
    }

    public function delete(Venta $venta): bool
    {
        // Soft delete: establece deleted_at, preserva FK en detalle_ventas
        return $venta->delete();
    }
}
