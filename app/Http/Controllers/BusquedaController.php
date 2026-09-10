<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\TurismoItem;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    // Secciones estáticas del portal con sus palabras clave
    private const SECCIONES = [
        [
            'titulo'    => 'Trámites y Servicios',
            'desc'      => 'Accedé a gestiones y trámites municipales.',
            'url'       => 'tramites-servicios.index',
            'icono'     => 'fa-file-lines',
            'keywords'  => ['trámite', 'tramite', 'servicio', 'gestión', 'gestion', 'municipal'],
        ],
        [
            'titulo'    => 'Reclamos',
            'desc'      => 'Ingresá un reclamo al municipio.',
            'url'       => 'reclamos.index',
            'icono'     => 'fa-comment-dots',
            'keywords'  => ['reclamo', 'queja', 'denuncia', 'problema'],
        ],
        [
            'titulo'    => 'Infracciones de Tránsito',
            'desc'      => 'Consultá tus infracciones de tránsito.',
            'url'       => 'infracciones.index',
            'icono'     => 'fa-car',
            'keywords'  => ['infracción', 'infraccion', 'multa', 'transito', 'tránsito'],
        ],
        [
            'titulo'    => 'Expedientes',
            'desc'      => 'Consultá el estado de un expediente municipal.',
            'url'       => 'expedientes.index',
            'icono'     => 'fa-folder-open',
            'keywords'  => ['expediente', 'número', 'numero', 'estado', 'consulta'],
        ],
        [
            'titulo'    => 'Habilitaciones Comerciales',
            'desc'      => 'Verificá habilitaciones de comercios.',
            'url'       => 'habilitaciones.index',
            'icono'     => 'fa-store',
            'keywords'  => ['habilitación', 'habilitacion', 'comercio', 'negocio', 'local'],
        ],
        [
            'titulo'    => 'Tasas Municipales',
            'desc'      => 'Consultá tasas y fechas de vencimiento.',
            'url'       => 'tasas.index',
            'icono'     => 'fa-file-invoice-dollar',
            'keywords'  => ['tasa', 'vencimiento', 'impuesto', 'pago', 'inmueble', 'automotor'],
        ],
        [
            'titulo'    => 'Gobierno Abierto',
            'desc'      => 'Información pública, licitaciones y documentos institucionales.',
            'url'       => 'gobierno-abierto.index',
            'icono'     => 'fa-landmark',
            'keywords'  => ['gobierno', 'abierto', 'transparencia', 'institucional', 'municipal'],
        ],
        [
            'titulo'    => 'Ordenanza Vigente',
            'desc'      => 'Ordenanza impositiva anual y normativa vigente del municipio.',
            'url'       => 'gobierno-abierto.index',
            'icono'     => 'fa-scale-balanced',
            'keywords'  => ['ordenanza', 'normativa', 'vigente', 'impositiva', 'decreto'],
        ],
        [
            'titulo'    => 'Licitaciones',
            'desc'      => 'Licitaciones públicas y privadas del municipio.',
            'url'       => 'licitaciones.index',
            'icono'     => 'fa-file-contract',
            'keywords'  => ['licitación', 'licitacion', 'contrato', 'concurso', 'adjudicación', 'adjudicacion'],
        ],
        [
            'titulo'    => 'Nómina de Empleados',
            'desc'      => 'Listado de empleados municipales.',
            'url'       => 'gobierno-abierto.index',
            'icono'     => 'fa-users',
            'keywords'  => ['nómina', 'nomina', 'empleado', 'personal', 'planta', 'agente'],
        ],
        [
            'titulo'    => 'Gastos, Recursos y Balance',
            'desc'      => 'Información presupuestaria del municipio.',
            'url'       => 'gastos-recursos-balance.index',
            'icono'     => 'fa-calculator',
            'keywords'  => ['gasto', 'recurso', 'balance', 'presupuesto', 'financiero', 'ejecución', 'ejecucion'],
        ],
        [
            'titulo'    => 'Informes Viales',
            'desc'      => 'Informes y documentación vial del municipio.',
            'url'       => 'gobierno-abierto.index',
            'icono'     => 'fa-road',
            'keywords'  => ['vial', 'viales', 'calle', 'camino', 'asfalto', 'pavimento'],
        ],
        [
            'titulo'    => 'Organigrama Municipal',
            'desc'      => 'Estructura municipal, jerarquías y dependencias.',
            'url'       => 'gobierno-abierto.index',
            'icono'     => 'fa-sitemap',
            'keywords'  => ['organigrama', 'estructura', 'jerarquía', 'jerarquia', 'secretaría', 'secretaria', 'área', 'area'],
        ],
        [
            'titulo'    => 'Obras Particulares',
            'desc'      => 'Solicitudes de obras y planos.',
            'url'       => 'obras-particulares.index',
            'icono'     => 'fa-helmet-safety',
            'keywords'  => ['obra', 'plano', 'construcción', 'construccion', 'permiso', 'edificación'],
        ],
        [
            'titulo'    => 'OMIC — Defensa del Consumidor',
            'desc'      => 'Denunciá un problema como consumidor.',
            'url'       => 'omic.index',
            'icono'     => 'fa-shield-halved',
            'keywords'  => ['omic', 'consumidor', 'defensa', 'denuncia', 'comercio'],
        ],
        [
            'titulo'    => 'Carnet de Conducir',
            'desc'      => 'Información para tramitar el carnet de conducir.',
            'url'       => 'carnet.index',
            'icono'     => 'fa-id-card',
            'keywords'  => ['carnet', 'licencia', 'conducir', 'registro', 'manejo'],
        ],
        [
            'titulo'    => 'Teléfonos Útiles',
            'desc'      => 'Contactos de emergencia y dependencias municipales.',
            'url'       => 'telefonos-utiles.index',
            'icono'     => 'fa-phone',
            'keywords'  => ['teléfono', 'telefono', 'contacto', 'emergencia', 'bombero', 'policía', 'policia', 'hospital'],
        ],
        [
            'titulo'    => 'Proveedores',
            'desc'      => 'Información para proveedores del municipio.',
            'url'       => 'proveedores.index',
            'icono'     => 'fa-truck',
            'keywords'  => ['proveedor', 'proveedor', 'contratación', 'contratacion', 'compra'],
        ],
        [
            'titulo'    => 'Turismo',
            'desc'      => 'Localidades, eventos y atractivos del partido de Chacabuco.',
            'url'       => 'turismo.index',
            'icono'     => 'fa-map-location-dot',
            'keywords'  => ['turismo', 'turista', 'visitar', 'atractivo', 'evento', 'localidad', 'chacabuco', 'rawson', 'higgins', 'castilla'],
        ],
    ];

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $noticias  = collect();
        $items     = collect();
        $secciones = collect();
        $palabras  = [];

        if (mb_strlen($q) >= 3) {
            $palabras = array_filter(array_unique(preg_split('/\s+/', mb_strtolower($q))));

            $noticias = Noticia::with('categorias')
                ->where('estado', 'publicado')
                ->where(function ($query) use ($palabras) {
                    foreach ($palabras as $p) {
                        $query->where(function ($q2) use ($p) {
                            $q2->where('titulo', 'like', "%{$p}%")
                               ->orWhere('contenido', 'like', "%{$p}%");
                        });
                    }
                })
                ->orderByDesc('fecha')
                ->limit(12)
                ->get();

            $items = TurismoItem::visible()
                ->where('mostrar_detalle', true)
                ->where(function ($query) use ($palabras) {
                    foreach ($palabras as $p) {
                        $query->where(function ($q2) use ($p) {
                            $q2->where('titulo', 'like', "%{$p}%")
                               ->orWhere('descripcion', 'like', "%{$p}%");
                        });
                    }
                })
                ->with('localidad')
                ->orderBy('titulo')
                ->limit(12)
                ->get();

            $secciones = $this->buscarSecciones($q);
        }

        $total = $noticias->count() + $items->count() + $secciones->count();

        return view('busqueda.index', compact('q', 'noticias', 'items', 'secciones', 'total', 'palabras'));
    }

    private function buscarSecciones(string $q): \Illuminate\Support\Collection
    {
        $palabras = array_filter(preg_split('/\s+/', mb_strtolower($q)));

        return collect(self::SECCIONES)->filter(function ($seccion) use ($palabras) {
            foreach ($palabras as $palabra) {
                foreach ($seccion['keywords'] as $keyword) {
                    if (str_contains(mb_strtolower($keyword), $palabra) || str_contains($palabra, mb_strtolower($keyword))) {
                        return true;
                    }
                }
            }
            return false;
        })->values();
    }
}
