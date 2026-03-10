<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use App\Services\Contracts\ProductoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function __construct(
        private readonly ProductoServiceInterface $productoService
    ) {}

    /**
     * GET /api/v1/productos
     * Query params: ?search=&per_page=&categoria_id=&activo=&con_eliminados=
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'codigo_barras',
            'per_page',
            'categoria_id',
            'activo',
            'con_eliminados',
        ]);

        $paginado = $this->productoService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Productos obtenidos correctamente.',
            data: ProductoResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/productos
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $producto = $this->productoService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Producto creado correctamente.',
            data: new ProductoResource($producto->load('categoria')),
            status: 201,
        );
    }

    /**
     * GET /api/v1/productos/{producto}
     */
    public function show(Producto $producto): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Producto obtenido correctamente.',
            data: new ProductoResource($producto->load('categoria')),
        );
    }

    /**
     * PUT|PATCH /api/v1/productos/{producto}
     */
    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse
    {
        $actualizado = $this->productoService->actualizar($producto, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Producto actualizado correctamente.',
            data: new ProductoResource($actualizado),
        );
    }

    /**
     * DELETE /api/v1/productos/{producto}
     * Soft delete — el registro permanece en la BD con deleted_at
     */
    public function destroy(Producto $producto): JsonResponse
    {
        $this->productoService->eliminar($producto);

        return response()->json(null, 204);
    }
}
