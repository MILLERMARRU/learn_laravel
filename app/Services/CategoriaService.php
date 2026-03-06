<?php

namespace App\Services;

use App\Models\Categoria;
use App\Repositories\Contracts\CategoriaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoriaService
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

    public function actualizar(int $id, array $data): Categoria
    {
        return $this->categoriaRepository->update($id, $data);
    }

    public function eliminar(int $id): bool
    {
        return $this->categoriaRepository->delete($id);
    }
}
