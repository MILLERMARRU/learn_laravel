<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // $this->route('producto') devuelve el modelo resuelto por Route Model Binding
        $productoId = $this->route('producto')?->id;

        return [
            'cod_producto'      => "sometimes|required|string|max:100|unique:productos,cod_producto,{$productoId}",
            'nombre'            => 'sometimes|required|string|max:255',
            'categoria_id'      => 'sometimes|required|integer|exists:categorias,id',
            'marca'             => 'nullable|string|max:150',
            'unidad_medida'     => 'sometimes|required|string|max:50',
            'contenido'         => 'nullable|string|max:100',
            'precio_compra'     => 'sometimes|required|numeric|min:0',
            'precio_minorista'  => 'sometimes|required|numeric|min:0',
            'precio_mayorista'  => 'sometimes|required|numeric|min:0',
            'stock_minimo'      => 'sometimes|required|integer|min:0',
            'activo'            => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'cod_producto.unique'   => 'Ya existe un producto con ese código.',
            'categoria_id.exists'   => 'La categoría seleccionada no existe.',
        ];
    }
}
