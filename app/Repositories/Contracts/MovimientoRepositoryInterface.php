<?php

namespace App\Repositories\Contracts;

use App\Models\Movimiento;
use Illuminate\Pagination\LengthAwarePaginator;

interface MovimientoRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id): ?Movimiento;

    public function create(array $data): Movimiento;
}
