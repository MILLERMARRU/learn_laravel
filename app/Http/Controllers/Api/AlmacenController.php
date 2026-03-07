<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlmacenRequest;
use App\Http\Requests\UpdateAlmacenRequest;
use App\Http\Resources\AlmacenResource;
use App\Models\Almacen;
use App\Services\Contracts\AlmacenServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function __construct(
        private readonly AlmacenServiceInterface $almacenService
    ) {}

    /**
     * GET /api/v1/almacenes
     * Query params: ?search= &activo= &con_eliminados= &per_page=
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'activo', 'con_eliminados', 'per_page']);

        $paginado = $this->almacenService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Almacenes obtenidos correctamente.',
            data: AlmacenResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/almacenes
     */
    public function store(StoreAlmacenRequest $request): JsonResponse
    {
        $almacen = $this->almacenService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Almacén creado correctamente.',
            data: new AlmacenResource($almacen),
            status: 201,
        );
    }

    /**
     * GET /api/v1/almacenes/{almacen}
     */
    public function show(Almacen $almacen): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Almacén obtenido correctamente.',
            data: new AlmacenResource($almacen),
        );
    }

    /**
     * PUT|PATCH /api/v1/almacenes/{almacen}
     */
    public function update(UpdateAlmacenRequest $request, Almacen $almacen): JsonResponse
    {
        $actualizado = $this->almacenService->actualizar($almacen, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Almacén actualizado correctamente.',
            data: new AlmacenResource($actualizado),
        );
    }

    /**
     * DELETE /api/v1/almacenes/{almacen}
     * Soft delete — el registro permanece en la BD con deleted_at
     */
    public function destroy(Almacen $almacen): JsonResponse
    {
        $this->almacenService->eliminar($almacen);

        return response()->json(null, 204);
    }
}
