<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use App\Services\PublicAccessButtonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GobiernoAbiertoController extends Controller
{
    public function index(PublicAccessButtonService $buttons)
    {
        $items = [
            [
                'clave' => 'licitaciones',
                'titulo' => 'Licitaciones',
                'descripcion' => 'Accede a las licitaciones publicas y privadas del Municipio de Chacabuco.',
                'icono' => 'fa-file-contract',
                'url' => route('licitaciones.index'),
                'destacado' => true,
            ],
            [
                'clave' => 'boletin-oficial-municipal',
                'titulo' => 'Boletin Oficial Municipal',
                'descripcion' => 'Acceso al boletin oficial municipal.',
                'icono' => 'fa-newspaper',
                'url' => 'https://sibom.slyt.gba.gob.ar/',
                'destacado' => false,
            ],
            [
                'clave' => 'consulta-proveedores',
                'titulo' => 'Consulta Proveedores',
                'descripcion' => 'Seguimiento de documentacion para proveedores.',
                'icono' => 'fa-user-tie',
                'url' => route('proveedores.index'),
                'destacado' => true,
            ],
            $this->botonDocumento(
                'ordenanza-vigente',
                'Ordenanza Vigente',
                'Ordenanza impositiva anual y normativa vigente.',
                'fa-scale-balanced',
                ['ordenanza']
            ),
            $this->botonDocumento(
                'nomina-de-empleados',
                'Nomina de empleados',
                'Listado de empleados municipales.',
                'fa-users',
                ['nomina', 'nómina', 'empleado']
            ),
            $this->botonDocumento(
                'informes-viales',
                'Informes Viales',
                'Informes y documentacion vial del municipio.',
                'fa-road',
                ['informe', 'informes', 'vial', 'viales']
            ),
            [
                'clave' => 'gastos-recursos-balance',
                'titulo' => 'Gastos, Recursos y Balance',
                'descripcion' => 'Informacion presupuestaria del municipio.',
                'icono' => 'fa-calculator',
                'url' => route('gastos-recursos-balance.index'),
                'destacado' => true,
            ],
            $this->botonDocumento(
                'organigrama',
                'Organigrama',
                'Estructura municipal, jerarquias y dependencias.',
                'fa-sitemap',
                ['organigrama']
            ),
            [
                'clave' => 'gis',
                'titulo' => 'Gis',
                'descripcion' => 'Sistemas de Información Geográfica',
                'icono' => 'fa-signs-post',
                'url' => 'http://gis.chacabuco.gob.ar/home/',
                'destacado' => false,
            ],
        ];

        $items = $buttons->gobiernoAbiertoVisibles($items);

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
            $url = $licitacion->link_externo;
            if (!in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'])) {
                abort(400);
            }
            return redirect()->away($url);
        }

        if ($licitacion->archivos->count() === 1) {
            return redirect($licitacion->archivos->first()->ruta);
        }

        return view('gobierno-abierto.accesos.show', [
            'documento' => $licitacion,
            'archivos' => $licitacion->archivos->sortByDesc('created_at'),
        ]);
    }

    private function botonDocumento(string $clave, string $titulo, string $descripcion, string $icono, array $terminos): array
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
            'clave' => $clave,
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
