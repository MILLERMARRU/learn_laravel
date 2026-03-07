<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_producto'      => 'required|string|max:100|unique:productos,cod_producto',
            'nombre'            => 'required|string|max:255',
            'categoria_id'      => 'required|integer|exists:categorias,id',
            'marca'             => 'nullable|string|max:150',
            'unidad_medida'     => 'required|string|max:50',
            'contenido'         => 'nullable|string|max:100',
            'precio_compra'     => 'required|numeric|min:0',
            'precio_minorista'  => 'required|numeric|min:0',
            'precio_mayorista'  => 'required|numeric|min:0',
            'stock_minimo'      => 'required|integer|min:0',
            'activo'            => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'cod_producto.required' => 'El código de producto es obligatorio.',
            'cod_producto.unique'   => 'Ya existe un producto con ese código.',
            'nombre.required'       => 'El nombre del producto es obligatorio.',
            'categoria_id.exists'   => 'La categoría seleccionada no existe.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_minorista.required' => 'El precio minorista es obligatorio.',
            'precio_mayorista.required' => 'El precio mayorista es obligatorio.',
            'stock_minimo.required'  => 'El stock mínimo es obligatorio.',
        ];
    }
}
