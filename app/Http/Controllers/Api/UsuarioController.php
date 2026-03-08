<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use App\Services\Contracts\UsuarioServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioServiceInterface $usuarioService
    ) {}

    /**
     * GET /api/v1/usuarios
     * Query params: ?search=miller&rol_id=1&activo=true&con_eliminados=true&per_page=15
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'rol_id', 'activo', 'con_eliminados']);

        $paginado = $this->usuarioService->listar($filters);

        return $this->apiResponse(
            success: true,
            message: 'Usuarios obtenidos correctamente.',
            data: UsuarioResource::collection($paginado->getCollection()),
        );
    }

    /**
     * POST /api/v1/usuarios
     */
    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = $this->usuarioService->crear($request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Usuario creado correctamente.',
            data: new UsuarioResource($usuario),
            status: 201,
        );
    }

    /**
     * GET /api/v1/usuarios/{usuario}
     */
    public function show(Usuario $usuario): JsonResponse
    {
        return $this->apiResponse(
            success: true,
            message: 'Usuario obtenido correctamente.',
            data: new UsuarioResource($usuario),
        );
    }

    /**
     * PUT|PATCH /api/v1/usuarios/{usuario}
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $actualizado = $this->usuarioService->actualizar($usuario, $request->validated());

        return $this->apiResponse(
            success: true,
            message: 'Usuario actualizado correctamente.',
            data: new UsuarioResource($actualizado),
        );
    }

    /**
     * DELETE /api/v1/usuarios/{usuario}
     */
    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->usuarioService->eliminar($usuario);

        return response()->json(null, 204);
    }
}
