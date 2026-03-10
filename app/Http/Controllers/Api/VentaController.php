<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVentaRequest;
use App\Http\Requests\UpdateVentaRequest;
use App\Http\Resources\VentaResource;
use App\Models\Venta;
use App\Services\Contracts\VentaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function __construct(
        private readonly VentaServiceInterface $ventaService
    ) {}

    /**
     * GET /api/v1/ventas
     * Query params: ?search= &usuario_id= &almacen_id= &estado= &tipo_pago=
     *               &fecha_desde= &fecha_hasta= &con_eliminados=
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'usuario_id',
            'almacen_id',
            'estado',
            'tipo_pago',
            'fecha_desde',
            'fecha_hasta',
            'con_eliminados',
            'per_page',
        ]);

        $paginado = $this->ventaService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Ventas obtenidas correctamente.',
            data: VentaResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/ventas
     */
    public function store(StoreVentaRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), [
            'usuario_id' => auth('api')->id(),
            'estado'     => 'pendiente',
            'total'      => 0,
        ]);

        $venta = $this->ventaService->crear($data);

        return $this->apiResponse(
            success: true,
            message: 'Venta creada correctamente.',
            data: new VentaResource($venta),
            status: 201,
        );
    }

    /**
     * GET /api/v1/ventas/{venta}
     */
    public function show(Venta $venta): JsonResponse
    {
        $venta->load(['usuario', 'almacen']);

        return $this->apiResponse(
            success: true,
            message: 'Venta obtenida correctamente.',
            data: new VentaResource($venta),
        );
    }

    /**
     * PUT|PATCH /api/v1/ventas/{venta}
     */
    public function update(UpdateVentaRequest $request, Venta $venta): JsonResponse
    {
        $actualizada = $this->ventaService->actualizar($venta, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Venta actualizada correctamente.',
            data: new VentaResource($actualizada),
        );
    }

    /**
     * DELETE /api/v1/ventas/{venta}
     */
    public function destroy(Venta $venta): JsonResponse
    {
        $this->ventaService->eliminar($venta);

        return response()->json(null, 204);
    }
}
