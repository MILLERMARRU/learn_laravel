<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlmacenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'direccion'   => 'required|string|max:255',
            'responsable' => 'required|string|max:150',
            'telefono'    => 'nullable|string|max:20',
            'activo'      => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre del almacén es obligatorio.',
            'direccion.required'   => 'La dirección del almacén es obligatoria.',
            'responsable.required' => 'El responsable del almacén es obligatorio.',
        ];
    }
}
