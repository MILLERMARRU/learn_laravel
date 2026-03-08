<?php

namespace App\Repositories\Contracts;

use App\Models\Usuario;
use Illuminate\Pagination\LengthAwarePaginator;

interface UsuarioRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Usuario;

    public function create(array $data): Usuario;

    public function update(Usuario $usuario, array $data): Usuario;

    /** Soft delete */
    public function delete(Usuario $usuario): bool;
}
