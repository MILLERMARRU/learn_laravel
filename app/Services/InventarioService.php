<?php

namespace App\Services;

use App\Models\Inventario;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use App\Services\Contracts\InventarioServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InventarioService implements InventarioServiceInterface
{
    public function __construct(
        private readonly InventarioRepositoryInterface $inventarioRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->inventarioRepository->all($filters);
    }

    public function obtener(int $id): ?Inventario
    {
        return $this->inventarioRepository->find($id);
    }

    public function crear(array $data): Inventario
    {
        return $this->inventarioRepository->create($data);
    }

    public function actualizar(Inventario $inventario, array $data): Inventario
    {
        return $this->inventarioRepository->update($inventario, $data);
    }

    public function eliminar(Inventario $inventario): bool
    {
        return $this->inventarioRepository->delete($inventario);
    }
}
