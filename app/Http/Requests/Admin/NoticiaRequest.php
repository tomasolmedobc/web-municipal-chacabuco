<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NoticiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'           => ['required', 'string', 'max:255'],
            'contenido'        => ['required', 'string'],
            'categorias'       => ['nullable', 'array'],
            'categorias.*'     => ['exists:categorias,id'],
            'fecha'            => ['required', 'date'],
            'estado'           => ['required', 'in:oculto,publicado'],
            'destacada'        => ['nullable', 'boolean'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'archivos.*'       => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
            'destacada_dias'   => ['nullable', 'integer', 'min:1', 'max:30'],
            'video_url'        => ['nullable', 'string', 'max:2048'],
        ];
    }
}
