<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaileUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'dni'             => [
                'required',
                'digits_between:7,9',
                Rule::unique('baile_usuarios', 'dni')->ignore($this->route('usuario')),
            ],
            'codigo'          => ['required', 'string', 'size:8'],
            'disponibles'     => ['required', 'integer', 'min:0', 'max:10'],
        ];
    }
}
