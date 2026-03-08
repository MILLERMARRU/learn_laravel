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
            'usuario_id'          => 'required|integer|exists:usuarios,id',
            'almacen_id'          => 'required|integer|exists:almacenes,id',
            'fecha'               => 'required|date',
            'cliente'             => 'required|string|max:255',
            'total'               => 'required|numeric|min:0',
            'numero_comprobante'  => 'required|string|max:50|unique:ventas,numero_comprobante',
            'tipo_pago'           => 'required|string|in:efectivo,tarjeta,transferencia,otro',
            'estado'              => 'required|string|in:pendiente,completada,cancelada',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.required'         => 'El usuario es obligatorio.',
            'usuario_id.exists'           => 'El usuario seleccionado no existe.',
            'almacen_id.required'         => 'El almacén es obligatorio.',
            'almacen_id.exists'           => 'El almacén seleccionado no existe.',
            'fecha.required'              => 'La fecha es obligatoria.',
            'fecha.date'                  => 'El formato de fecha no es válido.',
            'cliente.required'            => 'El cliente es obligatorio.',
            'total.required'              => 'El total es obligatorio.',
            'total.min'                   => 'El total no puede ser negativo.',
            'numero_comprobante.required' => 'El número de comprobante es obligatorio.',
            'numero_comprobante.unique'   => 'Ya existe una venta con ese número de comprobante.',
            'tipo_pago.required'          => 'El tipo de pago es obligatorio.',
            'tipo_pago.in'                => 'El tipo de pago debe ser: efectivo, tarjeta, transferencia u otro.',
            'estado.required'             => 'El estado es obligatorio.',
            'estado.in'                   => 'El estado debe ser: pendiente, completada o cancelada.',
        ];
    }
}
