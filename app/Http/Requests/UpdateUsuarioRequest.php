<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el id del usuario desde la ruta para ignorarlo en unique
        $usuarioId = $this->route('usuario')?->id;

        return [
            'rol_id'               => 'sometimes|required|integer|exists:roles,id',
            'username'             => "sometimes|required|string|max:100|unique:usuarios,username,{$usuarioId}",
            'email'                => "sometimes|required|email|max:255|unique:usuarios,email,{$usuarioId}",
            // 'sometimes' → solo valida password si viene en el request
            'password'             => 'sometimes|required|string|min:8',
            'must_change_password' => 'boolean',
            'activo'               => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.exists'     => 'El rol seleccionado no existe.',
            'username.unique'   => 'Ya existe un usuario con ese username.',
            'email.email'       => 'El formato del email no es válido.',
            'email.unique'      => 'Ya existe un usuario con ese email.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
