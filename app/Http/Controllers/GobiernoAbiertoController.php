<?php

namespace App\Http\Controllers;

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
                'url' => '#',
                'destacado' => false,
            ],
            [
                'titulo' => 'Consulta Proveedores',
                'descripcion' => 'Seguimiento de documentacion para proveedores.',
                'icono' => 'fa-user-tie',
                'url' => '#',
                'destacado' => false,
            ],
            [
                'titulo' => 'Ordenanza Vigente',
                'descripcion' => 'Ordenanza impositiva anual y normativa vigente.',
                'icono' => 'fa-scale-balanced',
                'url' => '#',
                'destacado' => false,
            ],
            [
                'titulo' => 'Nomina de empleados',
                'descripcion' => 'Listado de empleados municipales.',
                'icono' => 'fa-users',
                'url' => '#',
                'destacado' => false,
            ],
            [
                'titulo' => 'Gastos, Recursos y Balance',
                'descripcion' => 'Informacion presupuestaria del municipio.',
                'icono' => 'fa-calculator',
                'url' => '#',
                'destacado' => true,
            ],
            [
                'titulo' => 'Organigrama',
                'descripcion' => 'Estructura municipal, jerarquias y dependencias.',
                'icono' => 'fa-sitemap',
                'url' => '#',
                'destacado' => false,
            ],
        ];

        return view('gobierno-abierto.index', compact('items'));
    }
}
