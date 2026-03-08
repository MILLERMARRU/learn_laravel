<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ventaId = $this->route('venta')?->id;

        return [
            'usuario_id'          => 'sometimes|required|integer|exists:usuarios,id',
            'almacen_id'          => 'sometimes|required|integer|exists:almacenes,id',
            'fecha'               => 'sometimes|required|date',
            'cliente'             => 'sometimes|required|string|max:255',
            'total'               => 'sometimes|required|numeric|min:0',
            'numero_comprobante'  => "sometimes|required|string|max:50|unique:ventas,numero_comprobante,{$ventaId}",
            'tipo_pago'           => 'sometimes|required|string|in:efectivo,tarjeta,transferencia,otro',
            'estado'              => 'sometimes|required|string|in:pendiente,completada,cancelada',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.exists'         => 'El usuario seleccionado no existe.',
            'almacen_id.exists'         => 'El almacén seleccionado no existe.',
            'fecha.date'                => 'El formato de fecha no es válido.',
            'total.min'                 => 'El total no puede ser negativo.',
            'numero_comprobante.unique' => 'Ya existe una venta con ese número de comprobante.',
            'tipo_pago.in'              => 'El tipo de pago debe ser: efectivo, tarjeta, transferencia u otro.',
            'estado.in'                 => 'El estado debe ser: pendiente, completada o cancelada.',
        ];
    }
}
