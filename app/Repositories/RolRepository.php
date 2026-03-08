<?php

namespace App\Repositories;

use App\Models\Rol;
use App\Repositories\Contracts\RolRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class RolRepository implements RolRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Rol::query();

        // Filtro por nombre si viene el query param ?search=
        if (! empty($filters['search'])) {
            $query->where('nombre', 'like', '%' . $filters['search'] . '%');
        }

        // Límite máximo de 100 para evitar queries masivos
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function find(int $id): ?Rol
    {
        return Rol::find($id);
    }

    public function create(array $data): Rol
    {
        return Rol::create($data);
    }

    public function update(Rol $rol, array $data): Rol
    {
        $rol->update($data);

        return $rol->fresh();
    }

    public function delete(Rol $rol): bool
    {
        // Hard delete: elimina el registro definitivamente
        return $rol->delete();
    }
}
