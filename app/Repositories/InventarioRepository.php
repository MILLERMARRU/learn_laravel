<?php

namespace App\Repositories;

use App\Models\Inventario;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InventarioRepository implements InventarioRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Inventario::with(['producto', 'almacen']);

        if (isset($filters['producto_id'])) {
            $query->where('producto_id', $filters['producto_id']);
        }

        if (isset($filters['almacen_id'])) {
            $query->where('almacen_id', $filters['almacen_id']);
        }

        // Muestra solo registros con stock por debajo del mínimo
        if (! empty($filters['bajo_minimo'])) {
            $query->whereColumn('cantidad', '<', 'cantidad_minima');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 500;

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function find(int $id): ?Inventario
    {
        return Inventario::with(['producto', 'almacen'])->find($id);
    }

    public function create(array $data): Inventario
    {
        return Inventario::create($data);
    }

    public function update(Inventario $inventario, array $data): Inventario
    {
        // Actualiza ultima_actualizacion cuando cambian las cantidades
        if (array_intersect_key($data, array_flip(['cantidad', 'cantidad_reservada']))) {
            $data['ultima_actualizacion'] = now();
        }

        $inventario->update($data);

        return $inventario->fresh(['producto', 'almacen']);
    }

    public function findByProductoAlmacen(int $productoId, int $almacenId): ?Inventario
    {
        return Inventario::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->first();
    }

    public function delete(Inventario $inventario): bool
    {
        // Hard delete — elimina definitivamente el registro de stock
        return $inventario->delete();
    }
}
