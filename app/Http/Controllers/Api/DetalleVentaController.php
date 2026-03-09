<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDetalleVentaRequest;
use App\Http\Resources\DetalleVentaResource;
use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Services\Contracts\DetalleVentaServiceInterface;
use Illuminate\Http\JsonResponse;

class DetalleVentaController extends Controller
{
    public function __construct(
        private readonly DetalleVentaServiceInterface $detalleVentaService
    ) {}

    /**
     * GET /api/v1/ventas/{venta}/detalles
     * Lista todos los detalles de una venta.
     */
    public function index(Venta $venta): JsonResponse
    {
        $detalles = $this->detalleVentaService->listar($venta);

        return $this->apiResponse(
            success: true,
            message: 'Detalles de venta obtenidos correctamente.',
            data: DetalleVentaResource::collection($detalles),
        );
    }

    /**
     * POST /api/v1/ventas/{venta}/detalles
     * Registra un detalle, crea el movimiento de salida y actualiza el total de la venta.
     */
    public function store(StoreDetalleVentaRequest $request, Venta $venta): JsonResponse
    {
        try {
            $detalle = $this->detalleVentaService->registrar($venta, $request->validated());
        } catch (\RuntimeException $e) {
            return $this->apiResponse(
                success: false,
                message: $e->getMessage(),
                status: 422,
            );
        }

        return $this->apiResponse(
            success: true,
            message: 'Detalle de venta registrado correctamente.',
            data: new DetalleVentaResource($detalle),
            status: 201,
        );
    }

    /**
     * GET /api/v1/ventas/{venta}/detalles/{detalle}
     * Muestra un detalle específico de la venta.
     */
    public function show(Venta $venta, DetalleVenta $detalle): JsonResponse
    {
        // Verificar que el detalle pertenece a la venta indicada en la URL
        if ($detalle->venta_id !== $venta->id) {
            return $this->apiResponse(
                success: false,
                message: 'El detalle no pertenece a la venta indicada.',
                status: 404,
            );
        }

        $detalle->load(['producto', 'almacen', 'movimiento']);

        return $this->apiResponse(
            success: true,
            message: 'Detalle de venta obtenido correctamente.',
            data: new DetalleVentaResource($detalle),
        );
    }
}
