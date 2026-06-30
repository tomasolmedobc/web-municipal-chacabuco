<?php

namespace App\Http\Requests\Admin;

use App\Models\TurismoItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TurismoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo     = TurismoItem::normalizarTipo($this->input('tipo'));
        $esEvento = $tipo === TurismoItem::TIPO_EVENTO;

        return [
            'tipo'         => ['required', Rule::in(array_keys(TurismoItem::TIPOS))],
            'localidad_id' => ['required', 'exists:localidades,id'],
            'titulo'       => ['required', 'string', 'max:255'],
            'descripcion'  => ['nullable', 'string'],
            'categoria'    => ['nullable', 'string', 'max:255'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'telefono'     => ['nullable', 'string', 'max:50'],
            'link_externo' => ['nullable', 'url', 'max:2048'],
            'fecha_inicio' => [$esEvento ? 'required' : 'nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio'  => ['nullable', 'date_format:H:i'],
            'estado'       => ['required', 'in:visible,oculto'],
            'orden'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'imagen'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'galeria'      => ['nullable', 'array'],
            'galeria.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'adjuntos'     => ['nullable', 'array'],
            'adjuntos.*'   => ['file', 'mimes:pdf,doc,docx,xls,xlsx,zip', 'max:20480'],
            'video_url'    => ['nullable', 'string', 'max:2048'],
        ];
    }
}
