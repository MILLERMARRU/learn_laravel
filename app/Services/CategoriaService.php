<?php

namespace App\Services;

use App\Models\Categoria;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use App\Services\Contracts\CategoriaServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoriaService implements CategoriaServiceInterface
{
    public function __construct(
        private readonly CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->categoriaRepository->all($filters);
    }

    public function obtener(int $id): ?Categoria
    {
        return $this->categoriaRepository->find($id);
    }

    public function crear(array $data): Categoria
    {
        return $this->categoriaRepository->create($data);
    }

    public function actualizar(Categoria $categoria, array $data): Categoria
    {
        return $this->categoriaRepository->update($categoria, $data);
    }

    public function eliminar(Categoria $categoria): bool
    {
        return $this->categoriaRepository->delete($categoria);
    }
}
