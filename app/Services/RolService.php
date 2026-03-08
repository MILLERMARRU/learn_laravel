<?php

namespace App\Services;

use App\Models\Rol;
use App\Repositories\Contracts\RolRepositoryInterface;
use App\Services\Contracts\RolServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class RolService implements RolServiceInterface
{
    public function __construct(
        private readonly RolRepositoryInterface $rolRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->rolRepository->all($filters);
    }

    public function obtener(int $id): ?Rol
    {
        return $this->rolRepository->find($id);
    }

    public function crear(array $data): Rol
    {
        return $this->rolRepository->create($data);
    }

    public function actualizar(Rol $rol, array $data): Rol
    {
        return $this->rolRepository->update($rol, $data);
    }

    public function eliminar(Rol $rol): bool
    {
        // withTrashed() porque usuarios soft-deleted siguen teniendo la FK en la BD
        if ($rol->usuarios()->withTrashed()->exists()) {
            throw new \RuntimeException(
                'No se puede eliminar el rol porque está asignado a uno o más usuarios.'
            );
        }

        return $this->rolRepository->delete($rol);
    }
}
