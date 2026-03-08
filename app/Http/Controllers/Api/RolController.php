<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use App\Http\Resources\RolResource;
use App\Models\Rol;
use App\Services\Contracts\RolServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function __construct(
        private readonly RolServiceInterface $rolService
    ) {}

    /**
     * GET /api/v1/roles
     * Query params: ?search=admin&per_page=10
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);

        $paginado = $this->rolService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Roles obtenidos correctamente.',
            data: RolResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/roles
     */
    public function store(StoreRolRequest $request): JsonResponse
    {
        $rol = $this->rolService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Rol creado correctamente.',
            data: new RolResource($rol),
            status: 201,
        );
    }

    /**
     * GET /api/v1/roles/{rol}
     */
    public function show(Rol $rol): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Rol obtenido correctamente.',
            data: new RolResource($rol),
        );
    }

    /**
     * PUT|PATCH /api/v1/roles/{rol}
     */
    public function update(UpdateRolRequest $request, Rol $rol): JsonResponse
    {
        $actualizado = $this->rolService->actualizar($rol, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Rol actualizado correctamente.',
            data: new RolResource($actualizado),
        );
    }

    /**
     * DELETE /api/v1/roles/{rol}
     */
    public function destroy(Rol $rol): JsonResponse
    {
        try {
            $this->rolService->eliminar($rol);
        } catch (\RuntimeException $e) {
            return $this->apiResponse(
                success: false,
                message: $e->getMessage(),
                status: 409,
            );
        }

        return response()->json(null, 204);
    }
}
