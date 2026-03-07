<?php

namespace App\Services\Contracts;

use App\Models\Categoria;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoriaServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Categoria;

    public function crear(array $data): Categoria;

    public function actualizar(Categoria $categoria, array $data): Categoria;

    public function eliminar(Categoria $categoria): bool;
}
