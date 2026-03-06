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

        $perPage = $filters['per_page'] ?? 15;

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Categoria
    {
        return Categoria::find($id);
    }

    public function create(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function update(int $id, array $data): Categoria
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update($data);

        return $categoria->fresh();
    }

    public function delete(int $id): bool
    {
        // Hard delete: elimina el registro definitivamente
        return Categoria::findOrFail($id)->delete();
    }
}
