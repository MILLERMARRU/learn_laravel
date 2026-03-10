<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Repositories\Contracts\DetalleVentaRepositoryInterface;
use App\Repositories\Contracts\InventarioRepositoryInterface;
use App\Repositories\Contracts\MovimientoRepositoryInterface;
use App\Repositories\Contracts\VentaRepositoryInterface;
use App\Services\Contracts\DetalleVentaServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DetalleVentaService implements DetalleVentaServiceInterface
{
    public function __construct(
        private readonly DetalleVentaRepositoryInterface $detalleVentaRepository,
        private readonly InventarioRepositoryInterface   $inventarioRepository,
        private readonly MovimientoRepositoryInterface   $movimientoRepository,
        private readonly VentaRepositoryInterface        $ventaRepository,
    ) {}

    public function listar(Venta $venta): Collection
    {
        return $this->detalleVentaRepository->allByVenta($venta->id);
    }

    public function obtener(int $id): ?DetalleVenta
    {
        return $this->detalleVentaRepository->find($id);
    }

    /**
     * Registra un detalle de venta dentro de una transacción:
     * 1. Verifica que la venta esté pendiente
     * 2. Verifica inventario y stock suficiente
     * 3. Crea el movimiento de salida (descuenta inventario)
     * 4. Crea el detalle con movimiento_id y sub_total calculado
     * 5. Recalcula el total de la venta
     */
    public function registrar(Venta $venta, array $data): DetalleVenta
    {
        return DB::transaction(function () use ($venta, $data) {
            // 1. Solo se pueden agregar detalles a ventas pendientes
            if ($venta->estado !== 'pendiente') {
                throw new \RuntimeException(
                    "No se pueden agregar detalles a una venta con estado '{$venta->estado}'. Solo ventas pendientes."
                );
            }

            // 2. almacen_id se hereda de la venta (trazabilidad de sede)
            $almacenId  = $venta->almacen_id;
            $productoId = $data['producto_id'];
            $cantidad   = $data['cantidad'];

            $inventario = $this->inventarioRepository->findByProductoAlmacen($productoId, $almacenId);

            if (! $inventario) {
                throw new \RuntimeException(
                    'No existe un registro de inventario para ese producto en el almacén de la venta.'
                );
            }

            if ($inventario->cantidad < $cantidad) {
                throw new \RuntimeException(
                    "Stock insuficiente. Disponible: {$inventario->cantidad}."
                );
            }

            // 3. Crear movimiento de salida y descontar inventario
            $inventario->cantidad          -= $cantidad;
            $inventario->ultima_actualizacion = now();
            $inventario->save();

            $movimiento = $this->movimientoRepository->create([
                'producto_id' => $productoId,
                'almacen_id'  => $almacenId,
                'usuario_id'  => $venta->usuario_id,
                'tipo'        => 'salida',
                'cantidad'    => $cantidad,
                'fecha'       => $venta->fecha,
                'descripcion' => "Detalle de venta #{$venta->id} — comprobante {$venta->numero_comprobante}",
            ]);

            // 4. Calcular sub_total y crear el detalle
            $subTotal = $cantidad * $data['precio_unitario'];

            $detalle = $this->detalleVentaRepository->create([
                'venta_id'        => $venta->id,
                'producto_id'     => $productoId,
                'almacen_id'      => $almacenId,
                'movimiento_id'   => $movimiento->id,
                'cantidad'        => $cantidad,
                'precio_unitario' => $data['precio_unitario'],
                'sub_total'       => $subTotal,
            ]);

            // 5. Recalcular el total de la venta
            $venta->total = $this->detalleVentaRepository->sumarTotalVenta($venta->id);
            $venta->save();

            return $detalle->load(['producto', 'almacen', 'movimiento']);
        });
    }

    /**
     * Elimina un detalle de venta (solo si la venta está pendiente):
     * 1. Verifica que la venta esté pendiente
     * 2. Restaura el stock en inventario
     * 3. Elimina el movimiento de salida
     * 4. Elimina el detalle
     * 5. Recalcula el total de la venta
     */
    public function eliminar(Venta $venta, DetalleVenta $detalle): void
    {
        DB::transaction(function () use ($venta, $detalle) {
            if ($venta->estado !== 'pendiente') {
                throw new \RuntimeException(
                    "No se pueden eliminar detalles de una venta con estado '{$venta->estado}'."
                );
            }

            // Restaurar stock
            $inventario = $this->inventarioRepository->findByProductoAlmacen(
                $detalle->producto_id,
                $detalle->almacen_id
            );

            if ($inventario) {
                $inventario->cantidad += $detalle->cantidad;
                $inventario->ultima_actualizacion = now();
                $inventario->save();
            }

            // Guardar movimiento_id antes de borrar el detalle
            $movimientoId = $detalle->movimiento_id;

            // Eliminar detalle primero (libera la FK a movimientos)
            $this->detalleVentaRepository->delete($detalle);

            // Ahora sí se puede eliminar el movimiento
            if ($movimientoId) {
                $this->movimientoRepository->find($movimientoId)?->forceDelete();
            }

            // Recalcular total
            $venta->total = $this->detalleVentaRepository->sumarTotalVenta($venta->id);
            $venta->save();
        });
    }
}
