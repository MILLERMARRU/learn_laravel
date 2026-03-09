<?php

namespace App\Services;

use App\Models\Venta;
use App\Repositories\Contracts\VentaRepositoryInterface;
use App\Services\Contracts\VentaServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class VentaService implements VentaServiceInterface
{
    public function __construct(
        private readonly VentaRepositoryInterface $ventaRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->ventaRepository->all($filters);
    }

    public function obtener(int $id): ?Venta
    {
        return $this->ventaRepository->find($id);
    }

    public function crear(array $data): Venta
    {
        return $this->ventaRepository->create($data);
    }

    public function actualizar(Venta $venta, array $data): Venta
    {
        $usuario = auth('api')->user();

        // Vendedor solo puede cambiar el estado a 'completada' — nada más
        if ($usuario?->rol?->nombre === 'Vendedor') {
            if (count($data) !== 1 || ! isset($data['estado'])) {
                throw new \RuntimeException(
                    'Como vendedor solo puedes actualizar el estado de la venta.'
                );
            }

            if ($data['estado'] !== 'completada') {
                throw new \RuntimeException(
                    "Como vendedor solo puedes cambiar el estado a 'completada'."
                );
            }
        }

        return $this->ventaRepository->update($venta, $data);
    }

    public function eliminar(Venta $venta): bool
    {
        $venta->activo = false;
        $venta->save();

        return $this->ventaRepository->delete($venta);
    }
}
