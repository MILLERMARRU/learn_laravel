<?php

namespace App\Repositories\Contracts;

use App\Models\Categoria;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoriaRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Categoria;

    public function create(array $data): Categoria;

    public function update(Categoria $categoria, array $data): Categoria;

    public function delete(Categoria $categoria): bool;
}
