<?php

namespace App\Services\Contracts;

use App\Models\Inventario;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventarioServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Inventario;

    public function crear(array $data): Inventario;

    public function actualizar(Inventario $inventario, array $data): Inventario;

    /** Hard delete — seguro porque inventario no es FK en otras tablas */
    public function eliminar(Inventario $inventario): bool;
}
