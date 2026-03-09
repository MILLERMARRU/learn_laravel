<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleVentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'venta_id'        => $this->venta_id,
            'cantidad'        => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'sub_total'       => $this->sub_total,
            'producto'        => new ProductoResource($this->whenLoaded('producto')),
            'almacen'         => new AlmacenResource($this->whenLoaded('almacen')),
            'movimiento'      => new MovimientoResource($this->whenLoaded('movimiento')),
            'creado_en'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
