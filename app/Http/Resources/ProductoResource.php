<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'cod_producto'     => $this->cod_producto,
            'codigo_barras'    => $this->codigo_barras,
            'nombre'           => $this->nombre,
            'marca'            => $this->marca,
            'unidad_medida'    => $this->unidad_medida,
            'contenido'        => $this->contenido,
            'precio_compra'    => $this->precio_compra,
            'precio_minorista' => $this->precio_minorista,
            'precio_mayorista' => $this->precio_mayorista,
            'stock_minimo'     => $this->stock_minimo,
            'activo'           => $this->activo,
            'eliminado_en'     => $this->deleted_at?->toDateTimeString(),
            'categoria'        => new CategoriaResource($this->whenLoaded('categoria')),
            'creado_en'        => $this->created_at->toDateTimeString(),
            'actualizado_en'   => $this->updated_at->toDateTimeString(),
        ];
    }
}
