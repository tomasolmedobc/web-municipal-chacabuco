<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BaileAsientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'color'      => ['required', 'string', 'max:60'],
            'fila'       => ['required', 'string', 'max:10'],
            'numero'     => ['required', 'integer', 'min:1', 'max:999'],
            'disponible' => ['nullable', 'boolean'],
        ];
    }
}
