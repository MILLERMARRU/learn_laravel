<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlmacenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nombre'         => $this->nombre,
            'descripcion'    => $this->descripcion,
            'direccion'      => $this->direccion,
            'responsable'    => $this->responsable,
            'telefono'       => $this->telefono,
            'activo'         => $this->activo,
            'eliminado_en'   => $this->deleted_at?->toDateTimeString(),
            'creado_en'      => $this->created_at->toDateTimeString(),
            'actualizado_en' => $this->updated_at->toDateTimeString(),
        ];
    }
}
