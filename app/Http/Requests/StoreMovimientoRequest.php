<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id'  => 'required|integer|exists:productos,id',
            'almacen_id'   => 'required|integer|exists:almacenes,id',
            'usuario_id'   => 'required|integer|exists:usuarios,id',
            'tipo'         => 'required|string|in:entrada,salida',
            'cantidad'     => 'required|numeric|min:0.01',
            'fecha'        => 'required|date',
            'descripcion'  => 'nullable|string|max:500',
        ];
    }
}
