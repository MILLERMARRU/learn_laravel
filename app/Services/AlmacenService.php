<?php

namespace App\Services;

use App\Models\Almacen;
use App\Repositories\Contracts\AlmacenRepositoryInterface;
use App\Services\Contracts\AlmacenServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AlmacenService implements AlmacenServiceInterface
{
    public function __construct(
        private readonly AlmacenRepositoryInterface $almacenRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->almacenRepository->all($filters);
    }

    public function obtener(int $id): ?Almacen
    {
        return $this->almacenRepository->find($id);
    }

    public function crear(array $data): Almacen
    {
        return $this->almacenRepository->create($data);
    }

    public function actualizar(Almacen $almacen, array $data): Almacen
    {
        return $this->almacenRepository->update($almacen, $data);
    }

    public function eliminar(Almacen $almacen): bool
    {
        // Desactivar antes del soft delete para consistencia de estado
        $almacen->activo = false;
        $almacen->save();

        return $this->almacenRepository->delete($almacen);
    }
}
