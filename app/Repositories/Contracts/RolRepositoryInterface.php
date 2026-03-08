<?php

namespace App\Repositories\Contracts;

use App\Models\Rol;
use Illuminate\Pagination\LengthAwarePaginator;

interface RolRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Rol;

    public function create(array $data): Rol;

    public function update(Rol $rol, array $data): Rol;

    public function delete(Rol $rol): bool;
}
