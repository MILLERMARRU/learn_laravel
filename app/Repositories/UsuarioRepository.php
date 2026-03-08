<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Usuario::with('rol');

        // Filtro por username o email
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('username', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['rol_id'])) {
            $query->where('rol_id', $filters['rol_id']);
        }

        if (isset($filters['activo'])) {
            $query->where('activo', filter_var($filters['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        // Incluir eliminados (soft delete) solo si se solicita explícitamente
        if (! empty($filters['con_eliminados'])) {
            $query->withTrashed();
        }

        $perPage = min((int) ($filters['per_page'] ?? 15), 100);

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function find(int $id): ?Usuario
    {
        return Usuario::with('rol')->find($id);
    }

    public function create(array $data): Usuario
    {
        return Usuario::create($data);
    }

    public function update(Usuario $usuario, array $data): Usuario
    {
        $usuario->update($data);

        return $usuario->fresh('rol');
    }

    public function delete(Usuario $usuario): bool
    {
        // Soft delete: establece deleted_at, el registro permanece en la BD
        return $usuario->delete();
    }
}
