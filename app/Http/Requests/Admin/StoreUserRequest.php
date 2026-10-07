<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'apellido'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['required', Password::min(10), 'confirmed'],
            'rol'       => ['required', 'in:admin,editor'],
            'modulos'   => ['nullable', 'array'],
            'modulos.*' => ['string', 'in:' . implode(',', array_keys(User::MODULOS))],
        ];
    }
}
