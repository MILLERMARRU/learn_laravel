<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tipo'        => $this->tipo,
            'cantidad'    => $this->cantidad,
            'fecha'       => $this->fecha?->toDateString(),
            'descripcion' => $this->descripcion,
            'producto'    => new ProductoResource($this->whenLoaded('producto')),
            'almacen'     => new AlmacenResource($this->whenLoaded('almacen')),
            'usuario'     => new UsuarioResource($this->whenLoaded('usuario')),
            'creado_en'   => $this->created_at?->toDateTimeString(),
        ];
    }
}
