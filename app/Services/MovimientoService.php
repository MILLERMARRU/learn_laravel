<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use App\Repositories\Contracts\MovimientoRepositoryInterface;
use App\Services\Contracts\MovimientoServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MovimientoService implements MovimientoServiceInterface
{
    public function __construct(
        private readonly MovimientoRepositoryInterface $movimientoRepository,
        private readonly InventarioRepositoryInterface $inventarioRepository,
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->movimientoRepository->all($filters);
    }

    public function obtener(int $id): ?Movimiento
    {
        return $this->movimientoRepository->find($id);
    }

    /**
     * Registra el movimiento y actualiza la cantidad en inventario
     * dentro de una transacción para garantizar consistencia.
     */
    public function registrar(array $data): Movimiento
    {
        return DB::transaction(function () use ($data) {
            $inventario = $this->inventarioRepository->findByProductoAlmacen(
                $data['producto_id'],
                $data['almacen_id'],
            );

            if (! $inventario) {
                throw new \RuntimeException(
                    'No existe un registro de inventario para ese producto en ese almacén.'
                );
            }

            if ($data['tipo'] === 'salida') {
                if ($inventario->cantidad < $data['cantidad']) {
                    throw new \RuntimeException(
                        "Stock insuficiente. Disponible: {$inventario->cantidad}."
                    );
                }
                $inventario->cantidad -= $data['cantidad'];
            } else {
                $inventario->cantidad += $data['cantidad'];
            }

            $inventario->ultima_actualizacion = now();
            $inventario->save();

            return $this->movimientoRepository->create($data);
        });
    }
}
