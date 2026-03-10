<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'almacen_id'          => 'required|integer|exists:almacenes,id',
            'fecha'               => 'required|date',
            'tipo_pago'           => 'required|string|in:efectivo,tarjeta,transferencia,otro',
            'cliente'             => 'nullable|string|max:255',
            'numero_comprobante'  => 'nullable|string|max:50|unique:ventas,numero_comprobante',
        ];
    }

    public function messages(): array
    {
        return [
            'almacen_id.required' => 'El almacén es obligatorio.',
            'almacen_id.exists'   => 'El almacén seleccionado no existe.',
            'fecha.required'      => 'La fecha es obligatoria.',
            'fecha.date'          => 'El formato de fecha no es válido.',
            'tipo_pago.required'  => 'El tipo de pago es obligatorio.',
            'tipo_pago.in'        => 'El tipo de pago debe ser: efectivo, tarjeta, transferencia u otro.',
            'numero_comprobante.unique' => 'Ya existe una venta con ese número de comprobante.',
        ];
    }
}
