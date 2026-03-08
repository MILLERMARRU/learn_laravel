<?php

namespace App\Services\Contracts;

use App\Models\Movimiento;
use Illuminate\Pagination\LengthAwarePaginator;

interface MovimientoServiceInterface
{
    public function listar(array $filters): LengthAwarePaginator;

    public function obtener(int $id): ?Movimiento;

    public function registrar(array $data): Movimiento;
}
