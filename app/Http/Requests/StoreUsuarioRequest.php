<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rol_id'               => 'required|integer|exists:roles,id',
            'username'             => 'required|string|max:100|unique:usuarios,username',
            'email'                => 'required|email|max:255|unique:usuarios,email',
            'password'             => 'required|string|min:8',
            'must_change_password' => 'boolean',
            'activo'               => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'rol_id.required'   => 'El rol es obligatorio.',
            'rol_id.exists'     => 'El rol seleccionado no existe.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique'   => 'Ya existe un usuario con ese username.',
            'email.required'    => 'El email es obligatorio.',
            'email.email'       => 'El formato del email no es válido.',
            'email.unique'      => 'Ya existe un usuario con ese email.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
