<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|integer|exists:productos,id',
            'almacen_id'  => [
                'required',
                'integer',
                'exists:almacenes,id',
                // Valida que la combinación producto_id + almacen_id sea única
                Rule::unique('inventario')->where('producto_id', $this->producto_id),
            ],
            'cantidad'           => 'required|integer|min:0',
            'cantidad_reservada' => 'integer|min:0',
            'cantidad_minima'    => 'integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists'   => 'El producto seleccionado no existe.',
            'almacen_id.required'  => 'El almacén es obligatorio.',
            'almacen_id.exists'    => 'El almacén seleccionado no existe.',
            'almacen_id.unique'    => 'Este producto ya tiene un registro de inventario en ese almacén.',
            'cantidad.required'    => 'La cantidad es obligatoria.',
            'cantidad.min'         => 'La cantidad no puede ser negativa.',
        ];
    }
}
