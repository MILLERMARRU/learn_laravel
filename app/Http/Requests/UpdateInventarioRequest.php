<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad'           => 'sometimes|required|integer|min:0',
            'cantidad_reservada' => 'sometimes|required|integer|min:0',
            'cantidad_minima'    => 'sometimes|required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.min'           => 'La cantidad no puede ser negativa.',
            'cantidad_reservada.min' => 'La cantidad reservada no puede ser negativa.',
            'cantidad_minima.min'    => 'La cantidad mínima no puede ser negativa.',
        ];
    }
}
