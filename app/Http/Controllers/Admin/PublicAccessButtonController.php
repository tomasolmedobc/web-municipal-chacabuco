<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicAccessButton;
use App\Services\PublicAccessButtonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAccessButtonController extends Controller
{
    public function index(PublicAccessButtonService $buttons): View
    {
        return view('admin.public-access-buttons.index', [
            'secciones' => $buttons->sincronizarTodo($this->gobiernoAbiertoItems()),
            'titulos' => $this->titulosSecciones(),
        ]);
    }

    public function update(Request $request, PublicAccessButtonService $buttons): RedirectResponse
    {
        $buttons->sincronizarTodo($this->gobiernoAbiertoItems());

        $data = $request->validate([
            'activos' => ['array'],
            'activos.*' => ['boolean'],
            'links' => ['array'],
            'links.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $activos = collect($data['activos'] ?? []);
        $links = collect($data['links'] ?? []);

        PublicAccessButton::query()
            ->get()
            ->each(function (PublicAccessButton $button) use ($activos, $links) {
                $link = trim((string) $links->get((string) $button->id, ''));

                $button->update([
                    'activo' => $activos->has((string) $button->id),
                    'url_personalizada' => $link !== '' ? $link : null,
                ]);
            });

        return redirect()
            ->route('admin.botones-visibilidad.index')
            ->with('ok', 'Botones actualizados correctamente.');
    }

    private function gobiernoAbiertoItems(): array
    {
        return [
            [
                'clave' => 'licitaciones',
                'titulo' => 'Licitaciones',
                'descripcion' => 'Accede a las licitaciones publicas y privadas del Municipio de Chacabuco.',
                'icono' => 'fa-file-contract',
                'url' => route('licitaciones.index'),
            ],
            [
                'clave' => 'boletin-oficial-municipal',
                'titulo' => 'Boletin Oficial Municipal',
                'descripcion' => 'Acceso al boletin oficial municipal.',
                'icono' => 'fa-newspaper',
                'url' => 'https://sibom.slyt.gba.gob.ar/',
            ],
            [
                'clave' => 'consulta-proveedores',
                'titulo' => 'Consulta Proveedores',
                'descripcion' => 'Seguimiento de documentacion para proveedores.',
                'icono' => 'fa-user-tie',
                'url' => route('proveedores.index'),
            ],
            [
                'clave' => 'ordenanza-vigente',
                'titulo' => 'Ordenanza Vigente',
                'descripcion' => 'Ordenanza impositiva anual y normativa vigente.',
                'icono' => 'fa-scale-balanced',
            ],
            [
                'clave' => 'nomina-de-empleados',
                'titulo' => 'Nomina de empleados',
                'descripcion' => 'Listado de empleados municipales.',
                'icono' => 'fa-users',
            ],
            [
                'clave' => 'informes-viales',
                'titulo' => 'Informes Viales',
                'descripcion' => 'Informes y documentacion vial del municipio.',
                'icono' => 'fa-road',
            ],
            [
                'clave' => 'gastos-recursos-balance',
                'titulo' => 'Gastos, Recursos y Balance',
                'descripcion' => 'Informacion presupuestaria del municipio.',
                'icono' => 'fa-calculator',
                'url' => route('gastos-recursos-balance.index'),
            ],
            [
                'clave' => 'organigrama',
                'titulo' => 'Organigrama',
                'descripcion' => 'Estructura municipal, jerarquias y dependencias.',
                'icono' => 'fa-sitemap',
            ],
            [
                'clave' => 'gis',
                'titulo' => 'Gis',
                'descripcion' => 'Sistemas de Informacion Geografica',
                'icono' => 'fa-signs-post',
                'url' => 'http://gis.chacabuco.gob.ar/home/',
            ],
        ];
    }

    private function titulosSecciones(): array
    {
        return [
            PublicAccessButtonService::SECCION_TRAMITES => 'Tramites',
            PublicAccessButtonService::SECCION_SERVICIOS => 'Servicios',
            PublicAccessButtonService::SECCION_GOBIERNO_ABIERTO => 'Gobierno Abierto',
        ];
    }
}
