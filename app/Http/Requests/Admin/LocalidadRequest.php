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
            'nombre'         => ['sometimes', 'required', 'string', 'max:255'],
            'historia'       => ['nullable', 'string'],
            'descripcion'    => ['nullable', 'string', 'max:1000'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'mapa_embed'     => ['nullable', 'url', 'max:2048', 'starts_with:https://www.google.com/maps/embed'],
            'orden'          => ['nullable', 'integer', 'min:0', 'max:999'],
            'estado'         => ['required', 'in:visible,oculto'],
        ];
    }

    public function messages(): array
    {
        return [
            'mapa_embed.starts_with' => 'La URL del mapa debe ser la URL de inserción de Google Maps (empieza con https://www.google.com/maps/embed). No uses el link de "Compartir" — usá la pestaña "Insertar un mapa" y copiá el valor del atributo src="...".',
            'mapa_embed.url'         => 'La URL del mapa no tiene un formato válido.',
        ];
    }
}
