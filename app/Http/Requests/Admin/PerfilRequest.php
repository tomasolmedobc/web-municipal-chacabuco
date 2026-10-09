<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'nullable', 'string', 'current_password'],
            'password'         => ['nullable', 'string', Password::min(10), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required_with'    => 'Ingresá tu contraseña actual para poder cambiarla.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
        ];
    }
}
