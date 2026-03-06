<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dto\request\StoreCategoriaRequest;
use App\Http\Controllers\dto\request\UpdateCategoriaRequest;
use App\Http\Controllers\dto\response\CategoriaResource;
use App\Services\CategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct(
        private readonly CategoriaService $categoriaService
    ) {}

    /**
     * GET /api/v1/categorias
     * Query params: ?search=ropa&per_page=10
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'per_page']);

        $paginado = $this->categoriaService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Categorías obtenidas correctamente.',
            data: CategoriaResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/categorias
     */
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = $this->categoriaService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Categoría creada correctamente.',
            data: new CategoriaResource($categoria),
            status: 201,
        );
    }

    /**
     * GET /api/v1/categorias/{id}
     */
    public function show(int $id): JsonResponse
    {
        $categoria = $this->categoriaService->obtener($id);

        if (! $categoria) {
            return $this->apiResponse(
                success: false,
                message: 'Categoría no encontrada.',
                status: 404,
            );
        }

        return $this->apiResponse(
            success: true,
            message: 'Categoría obtenida correctamente.',
            data: new CategoriaResource($categoria),
        );
    }

    /**
     * PUT /api/v1/categorias/{id}
     */
    public function update(UpdateCategoriaRequest $request, int $id): JsonResponse
    {
        $categoria = $this->categoriaService->obtener($id);

        if (! $categoria) {
            return $this->apiResponse(
                success: false,
                message: 'Categoría no encontrada.',
                status: 404,
            );
        }

        $actualizada = $this->categoriaService->actualizar($id, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Categoría actualizada correctamente.',
            data: new CategoriaResource($actualizada),
        );
    }

    /**
     * DELETE /api/v1/categorias/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $categoria = $this->categoriaService->obtener($id);

        if (! $categoria) {
            return $this->apiResponse(
                success: false,
                message: 'Categoría no encontrada.',
                status: 404,
            );
        }

        $this->categoriaService->eliminar($id);

        return $this->apiResponse(
            success: true,
            message: 'Categoría eliminada correctamente.',
        );
    }
}
