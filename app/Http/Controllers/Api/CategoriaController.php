<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;
use App\Services\Contracts\CategoriaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function __construct(
        private readonly CategoriaServiceInterface $categoriaService
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
     * GET /api/v1/categorias/{categoria}
     */
    public function show(Categoria $categoria): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Categoría obtenida correctamente.',
            data: new CategoriaResource($categoria),
        );
    }

    /**
     * PUT|PATCH /api/v1/categorias/{categoria}
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria): JsonResponse
    {
        $actualizada = $this->categoriaService->actualizar($categoria, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Categoría actualizada correctamente.',
            data: new CategoriaResource($actualizada),
        );
    }

    /**
     * DELETE /api/v1/categorias/{categoria}
     */
    public function destroy(Categoria $categoria): JsonResponse
    {
        try {
            $this->categoriaService->eliminar($categoria);
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
