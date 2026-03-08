<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventarioRequest;
use App\Http\Requests\UpdateInventarioRequest;
use App\Http\Resources\InventarioResource;
use App\Models\Inventario;
use App\Services\Contracts\InventarioServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function __construct(
        private readonly InventarioServiceInterface $inventarioService
    ) {}

    /**
     * GET /api/v1/inventario
     * Query params: ?producto_id= &almacen_id= &bajo_minimo=
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['producto_id', 'almacen_id', 'bajo_minimo']);

        $paginado = $this->inventarioService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Inventario obtenido correctamente.',
            data: InventarioResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/inventario
     * Asigna un producto a un almacén con su stock inicial
     */
    public function store(StoreInventarioRequest $request): JsonResponse
    {
        $inventario = $this->inventarioService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Registro de inventario creado correctamente.',
            data: new InventarioResource($inventario->load(['producto', 'almacen'])),
            status: 201,
        );
    }

    /**
     * GET /api/v1/inventario/{inventario}
     */
    public function show(Inventario $inventario): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Registro de inventario obtenido correctamente.',
            data: new InventarioResource($inventario->load(['producto', 'almacen'])),
        );
    }

    /**
     * PUT|PATCH /api/v1/inventario/{inventario}
     * Actualización manual de cantidades (hasta que movimientos lo automatice)
     */
    public function update(UpdateInventarioRequest $request, Inventario $inventario): JsonResponse
    {
        $actualizado = $this->inventarioService->actualizar($inventario, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Inventario actualizado correctamente.',
            data: new InventarioResource($actualizado),
        );
    }

    /**
     * DELETE /api/v1/inventario/{inventario}
     * Hard delete — elimina el registro de stock definitivamente
     */
    public function destroy(Inventario $inventario): JsonResponse
    {
        $this->inventarioService->eliminar($inventario);

        return response()->json(null, 204);
    }
}
