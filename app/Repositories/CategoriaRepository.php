<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Categoria::query();

        // Filtro por nombre si viene el query param ?search=
        if (! empty($filters['search'])) {
            $query->where('nombre', 'like', '%' . $filters['search'] . '%');
        }

        // Límite máximo de 100 para evitar queries masivos
        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function find(int $id): ?Categoria
    {
        return Categoria::find($id);
    }

    public function create(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function update(Categoria $categoria, array $data): Categoria
    {
        $categoria->update($data);

        return $categoria->fresh();
    }

    public function delete(Categoria $categoria): bool
    {
        // Hard delete: elimina el registro definitivamente
        return $categoria->delete();
    }
}
