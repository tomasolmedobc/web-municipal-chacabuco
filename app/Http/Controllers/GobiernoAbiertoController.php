<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GobiernoAbiertoController extends Controller
{
    public function index()
    {
        $items = [
            [
                'titulo' => 'Licitaciones',
                'descripcion' => 'Accede a las licitaciones publicas y privadas del Municipio de Chacabuco.',
                'icono' => 'fa-file-contract',
                'url' => route('licitaciones.index'),
                'destacado' => true,
            ],
            [
                'titulo' => 'Boletin Oficial Municipal',
                'descripcion' => 'Acceso al boletin oficial municipal.',
                'icono' => 'fa-newspaper',
                'url' => 'https://sibom.slyt.gba.gob.ar/',
                'destacado' => false,
            ],
            [
                'titulo' => 'Consulta Proveedores',
                'descripcion' => 'Seguimiento de documentacion para proveedores.',
                'icono' => 'fa-user-tie',
                'url' => '#',
                'destacado' => false,
            ],
            $this->botonDocumento(
                'Ordenanza Vigente',
                'Ordenanza impositiva anual y normativa vigente.',
                'fa-scale-balanced',
                ['ordenanza']
            ),
            $this->botonDocumento(
                'Nomina de empleados',
                'Listado de empleados municipales.',
                'fa-users',
                ['nomina', 'nómina', 'empleado']
            ),
            [
                'titulo' => 'Gastos, Recursos y Balance',
                'descripcion' => 'Informacion presupuestaria del municipio.',
                'icono' => 'fa-calculator',
                'url' => route('gastos-recursos-balance.index'),
                'destacado' => true,
            ],
            $this->botonDocumento(
                'Organigrama',
                'Estructura municipal, jerarquias y dependencias.',
                'fa-sitemap',
                ['organigrama']
            ),
        ];

        return view('gobierno-abierto.index', compact('items'));
    }

    public function showAcceso(Licitacion $licitacion): View|RedirectResponse
    {
        abort_unless(
            $licitacion->categoria === Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS
                && $licitacion->estado === 'activa',
            404
        );

        $licitacion->load('archivos');

        if ($licitacion->link_externo) {
            return redirect()->away($licitacion->link_externo);
        }

        if ($licitacion->archivos->count() === 1) {
            return redirect($licitacion->archivos->first()->ruta);
        }

        return view('gobierno-abierto.accesos.show', [
            'documento' => $licitacion,
            'archivos' => $licitacion->archivos->sortByDesc('created_at'),
        ]);
    }

    private function botonDocumento(string $titulo, string $descripcion, string $icono, array $terminos): array
    {
        $documento = Licitacion::with('archivos')
            ->categoria(Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS)
            ->where('estado', 'activa')
            ->where(function ($query) use ($terminos) {
                foreach ($terminos as $termino) {
                    $query->orWhere('titulo', 'like', '%' . $termino . '%');
                }
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $archivo = $documento?->archivos
            ->sortByDesc('created_at')
            ->first();

        return [
            'titulo' => $titulo,
            'descripcion' => $documento?->descripcion ?: $descripcion,
            'icono' => $icono,
            'url' => $documento
                ? $this->urlDocumento($documento, $archivo)
                : '#',
            'destacado' => false,
        ];
    }

    private function urlDocumento(Licitacion $documento, $archivo): string
    {
        if ($documento->link_externo) {
            return $documento->link_externo;
        }

        if ($documento->archivos->count() === 1 && $archivo) {
            return $archivo->ruta;
        }

        if ($documento->archivos->count() > 1) {
            return route('gobierno-abierto.accesos.show', $documento);
        }

        return '#';
    }
}
