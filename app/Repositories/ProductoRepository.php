<?php

namespace App\Repositories;

use App\Models\Producto;
use App\Repositories\Contracts\ProductoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductoRepository implements ProductoRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Producto::with('categoria');

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('cod_producto', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['categoria_id'])) {
            $query->where('categoria_id', $filters['categoria_id']);
        }

        if (isset($filters['activo'])) {
            $query->where('activo', filter_var($filters['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        // Incluir eliminados (soft delete) solo si se solicita explícitamente
        if (! empty($filters['con_eliminados'])) {
            $query->withTrashed();
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Producto
    {
        return Producto::with('categoria')->find($id);
    }

    public function create(array $data): Producto
    {
        return Producto::create($data);
    }

    public function update(Producto $producto, array $data): Producto
    {
        $producto->update($data);

        return $producto->fresh('categoria');
    }

    public function delete(Producto $producto): bool
    {
        // Soft delete: establece deleted_at, el registro permanece en la BD
        return $producto->delete();
    }

}
