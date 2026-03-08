<?php

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Services\Contracts\UsuarioServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UsuarioService implements UsuarioServiceInterface
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->usuarioRepository->all($filters);
    }

    public function obtener(int $id): ?Usuario
    {
        return $this->usuarioRepository->find($id);
    }

    public function crear(array $data): Usuario
    {
        // Hashear la contraseña antes de persistir
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        return $this->usuarioRepository->create($data);
    }

    public function actualizar(Usuario $usuario, array $data): Usuario
    {
        // Si viene una nueva contraseña, hashearla
        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        return $this->usuarioRepository->update($usuario, $data);
    }

    public function eliminar(Usuario $usuario): bool
    {
        // Desactivar antes del soft delete para consistencia de estado
        $usuario->activo = false;
        $usuario->save();

        return $this->usuarioRepository->delete($usuario);
    }
}
