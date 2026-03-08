<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'fecha'               => $this->fecha?->toDateString(),
            'cliente'             => $this->cliente,
            'total'               => $this->total,
            'numero_comprobante'  => $this->numero_comprobante,
            'tipo_pago'           => $this->tipo_pago,
            'estado'              => $this->estado,
            'activo'              => $this->activo,
            'eliminado_en'        => $this->deleted_at?->toDateTimeString(),
            'usuario'             => new UsuarioResource($this->whenLoaded('usuario')),
            'almacen'             => new AlmacenResource($this->whenLoaded('almacen')),
            'creado_en'           => $this->created_at->toDateTimeString(),
            'actualizado_en'      => $this->updated_at->toDateTimeString(),
        ];
    }
}
