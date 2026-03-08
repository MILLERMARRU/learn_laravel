<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovimientoRequest;
use App\Http\Resources\MovimientoResource;
use App\Models\Movimiento;
use App\Services\Contracts\MovimientoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    public function __construct(
        private readonly MovimientoServiceInterface $movimientoService
    ) {}

    /**
     * GET /api/v1/movimientos
     * Query params: ?producto_id= &almacen_id= &usuario_id= &tipo= &fecha_desde= &fecha_hasta=
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'producto_id',
            'almacen_id',
            'usuario_id',
            'tipo',
            'fecha_desde',
            'fecha_hasta',
        ]);

        $paginado = $this->movimientoService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Movimientos obtenidos correctamente.',
            data: MovimientoResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/movimientos
     * Registra el movimiento y actualiza el stock en inventario automáticamente.
     */
    public function store(StoreMovimientoRequest $request): JsonResponse
    {
        try {
            $movimiento = $this->movimientoService->registrar($request->validated());
        } catch (\RuntimeException $e) {
            return $this->apiResponse(
                success: false,
                message: $e->getMessage(),
                status: 422,
            );
        }

        return $this->apiResponse(
            success: true,
            message: 'Movimiento registrado correctamente.',
            data: new MovimientoResource($movimiento),
            status: 201,
        );
    }

    /**
     * GET /api/v1/movimientos/{movimiento}
     */
    public function show(Movimiento $movimiento): JsonResponse
    {
        // Cargar relaciones para el resource
        $movimiento->load(['producto', 'almacen', 'usuario']);

        return $this->apiResponse(
            success: true,
            message: 'Movimiento obtenido correctamente.',
            data: new MovimientoResource($movimiento),
        );
    }
}
