<?php

namespace App\Repositories;

use App\Models\Almacen;
use App\Repositories\Contracts\AlmacenRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AlmacenRepository implements AlmacenRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Almacen::query();

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('responsable', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('direccion', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['activo'])) {
            $query->where('activo', filter_var($filters['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['con_eliminados'])) {
            $query->withTrashed();
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function find(int $id): ?Almacen
    {
        return Almacen::find($id);
    }

    public function create(array $data): Almacen
    {
        return Almacen::create($data);
    }

    public function update(Almacen $almacen, array $data): Almacen
    {
        $almacen->update($data);

        return $almacen->fresh();
    }

    public function delete(Almacen $almacen): bool
    {
        // Soft delete: establece deleted_at, preserva FK en ventas
        return $almacen->delete();
    }
}
