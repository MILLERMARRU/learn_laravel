<?php

namespace App\Repositories;

use App\Models\Movimiento;
use App\Repositories\Contracts\MovimientoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class MovimientoRepository implements MovimientoRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Movimiento::with(['producto', 'almacen', 'usuario']);

        if (isset($filters['producto_id'])) {
            $query->where('producto_id', $filters['producto_id']);
        }

        if (isset($filters['almacen_id'])) {
            $query->where('almacen_id', $filters['almacen_id']);
        }

        if (isset($filters['usuario_id'])) {
            $query->where('usuario_id', $filters['usuario_id']);
        }

        if (isset($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (! empty($filters['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filters['fecha_desde']);
        }

        if (! empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filters['fecha_hasta']);
        }

        return $query->orderBy('id', 'asc')->paginate(15);
    }

    public function find(int $id): ?Movimiento
    {
        return Movimiento::with(['producto', 'almacen', 'usuario'])->find($id);
    }

    public function create(array $data): Movimiento
    {
        return Movimiento::create($data);
    }
}
