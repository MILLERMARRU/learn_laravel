<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'username'             => $this->username,
            'email'                => $this->email,
            'must_change_password' => $this->must_change_password,
            'activo'               => $this->activo,
            'ultimo_acceso'        => $this->ultimo_acceso?->toDateTimeString(),
            'eliminado_en'         => $this->deleted_at?->toDateTimeString(),
            'rol'                  => new RolResource($this->whenLoaded('rol')),
            'creado_en'            => $this->created_at->toDateTimeString(),
            'actualizado_en'       => $this->updated_at->toDateTimeString(),
        ];
    }
}
