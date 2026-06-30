<?php

namespace App\Http\Requests\Admin;

use App\Models\HabilitacionDocumento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HabilitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $seccion     = HabilitacionDocumento::normalizarSeccion($this->input('seccion'));
        $esOrdenanza = $seccion === HabilitacionDocumento::SECCION_ORDENANZAS;

        // El archivo es requerido solo cuando no hay link externo ni se está editando
        $archivoRequerido = ! $this->route('habilitacion') && ! $this->filled('link_externo');

        return [
            'seccion'       => ['required', Rule::in(array_keys(HabilitacionDocumento::SECCIONES))],
            'titulo'        => ['required', 'string', 'max:255'],
            'descripcion'   => ['nullable', 'string'],
            'categoria'     => ['nullable', 'string', 'max:255'],
            'numero'        => [$esOrdenanza ? 'required' : 'nullable', 'string', 'max:80'],
            'anio'          => ['nullable', 'integer', 'between:1900,' . (date('Y') + 1)],
            'estado'        => ['required', 'in:visible,oculto'],
            'orden'         => ['nullable', 'integer', 'min:0', 'max:999'],
            'link_externo'  => ['nullable', 'url', 'max:2048'],
            'archivo'       => [
                $archivoRequerido ? 'required' : 'nullable',
                'file',
                $esOrdenanza ? 'mimes:pdf' : 'mimes:pdf,doc,docx',
                'max:10240',
            ],
        ];
    }
}
