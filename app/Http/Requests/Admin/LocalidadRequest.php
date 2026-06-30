<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LocalidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'historia'       => ['nullable', 'string'],
            'descripcion'    => ['nullable', 'string', 'max:1000'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'orden'          => ['nullable', 'integer', 'min:0', 'max:999'],
            'estado'         => ['required', 'in:visible,oculto'],
        ];
    }
}
