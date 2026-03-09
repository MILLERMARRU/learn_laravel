<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetalleVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id'     => 'required|integer|exists:productos,id',
            'cantidad'        => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0.01',
        ];
    }
}
