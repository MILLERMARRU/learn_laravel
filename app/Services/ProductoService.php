<?php

namespace App\Services;

use App\Models\Producto;
use App\Repositories\Contracts\ProductoRepositoryInterface;
use App\Services\Contracts\ProductoServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductoService implements ProductoServiceInterface
{
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository
    ) {}

    public function listar(array $filters): LengthAwarePaginator
    {
        return $this->productoRepository->all($filters);
    }

    public function obtener(int $id): ?Producto
    {
        return $this->productoRepository->find($id);
    }

    public function crear(array $data): Producto
    {
        return $this->productoRepository->create($data);
    }

    public function actualizar(Producto $producto, array $data): Producto
    {
        return $this->productoRepository->update($producto, $data);
    }

    public function eliminar(Producto $producto): bool
    {
        return $this->productoRepository->delete($producto);
    }
}
