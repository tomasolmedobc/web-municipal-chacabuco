<?php

namespace App\Http\Requests\Admin;

use App\Models\Licitacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoria = Licitacion::normalizarCategoria($this->input('categoria'));
        $esBoton   = $categoria === Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS;

        return [
            'categoria'          => ['required', Rule::in(array_keys(Licitacion::CATEGORIAS))],
            'titulo'             => ['required', 'string', 'max:255'],
            'descripcion'        => ['nullable', 'string'],
            'tipo'               => ['required', 'in:publica,privada'],
            'estado'             => ['required', 'in:activa,finalizada'],
            'numero_expediente'  => ['nullable', 'string', 'max:255'],
            'anio'               => ['nullable', 'integer'],
            'fecha_publicacion'  => ['nullable', 'date'],
            'link_externo'       => ['nullable', 'url', 'max:2048'],
            'orden'              => ['nullable', 'integer', 'min:0', 'max:999'],
            'archivos.*'         => [
                'nullable',
                'file',
                $esBoton ? 'mimes:pdf,doc,docx,xls,xlsx' : 'mimes:pdf',
                'max:10240',
            ],
        ];
    }
}
