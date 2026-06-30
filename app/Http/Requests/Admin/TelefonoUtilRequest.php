<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TelefonoUtilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:100'],
            'email'     => ['nullable', 'email', 'max:255'],
            'orden'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'estado'    => ['required', 'in:visible,oculto'],
        ];
    }
}
