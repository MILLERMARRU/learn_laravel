<?php

namespace App\Http\Controllers\dto\response;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'creado_en'   => $this->created_at->toDateTimeString(),
            'actualizado_en' => $this->updated_at->toDateTimeString(),
        ];
    }
}
