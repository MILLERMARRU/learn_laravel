<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'cantidad'             => $this->cantidad,
            'cantidad_reservada'   => $this->cantidad_reservada,
            'cantidad_disponible'  => $this->cantidad - $this->cantidad_reservada,
            'cantidad_minima'      => $this->cantidad_minima,
            'bajo_minimo'          => $this->cantidad < $this->cantidad_minima,
            'ultima_actualizacion' => $this->ultima_actualizacion?->toDateTimeString(),
            'producto'             => new ProductoResource($this->whenLoaded('producto')),
            'almacen'              => new AlmacenResource($this->whenLoaded('almacen')),
            'creado_en'            => $this->created_at->toDateTimeString(),
            'actualizado_en'       => $this->updated_at->toDateTimeString(),
        ];
    }
}
