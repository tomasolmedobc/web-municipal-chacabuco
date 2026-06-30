<?php

namespace App\Http\Controllers;

use App\Models\TelefonoUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function responder(Request $request): JsonResponse
    {
        $entrada = trim($request->input('mensaje', ''));

        if (mb_strlen($entrada) < 2) {
            return response()->json(['tipo' => 'vacio']);
        }

        $termino = $this->normalizar(mb_substr($entrada, 0, 200));

        $telefonos = $this->buscarTelefonos($termino);
        $seccion   = $this->buscarSeccion($termino);

        if ($telefonos->isNotEmpty()) {
            return response()->json([
                'tipo'      => 'telefonos',
                'texto'     => 'Encontré ' . $telefonos->count() . ' resultado' . ($telefonos->count() > 1 ? 's' : '') . ':',
                'telefonos' => $telefonos,
                'seccion'   => $seccion,
            ]);
        }

        if ($seccion) {
            return response()->json([
                'tipo'  => 'seccion',
                'texto' => $seccion['texto'],
                'url'   => $seccion['url'],
                'label' => $seccion['label'],
            ]);
        }

        return response()->json([
            'tipo'        => 'sin_resultados',
            'texto'       => 'No encontré resultados para "' . e($entrada) . '".',
            'sugerencias' => ['hospital', 'policía', 'municipio', 'bomberos', 'reclamo', 'expediente'],
        ]);
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));

        return str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'à', 'è', 'ì', 'ò', 'ù'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u'],
            $texto
        );
    }

    private function buscarTelefonos(string $termino): \Illuminate\Database\Eloquent\Collection
    {
        return TelefonoUtil::visible()
            ->where(function ($q) use ($termino) {
                $q->where('nombre',    'like', "%{$termino}%")
                  ->orWhere('categoria', 'like', "%{$termino}%")
                  ->orWhere('telefono',  'like', "%{$termino}%")
                  ->orWhere('direccion', 'like', "%{$termino}%");
            })
            ->orderBy('nombre')
            ->limit(6)
            ->get(['nombre', 'categoria', 'telefono', 'email', 'direccion']);
    }

    private function buscarSeccion(string $termino): ?array
    {
        $secciones = [
            [
                'keywords' => ['reclamo', 'queja', 'bache', 'basura', 'pozo', 'luz', 'agua', 'cloaca', 'vereda', 'arbol', 'poda', 'limpieza', 'aviso'],
                'texto'    => 'Podés registrar un reclamo municipal en línea.',
                'url'      => route('reclamos.index'),
                'label'    => 'Ir a Reclamos',
            ],
            [
                'keywords' => ['expediente', 'numero de expediente', 'tramite en curso', 'consultar expediente'],
                'texto'    => 'Podés consultar el estado de tu expediente ingresando el número.',
                'url'      => route('expedientes.index'),
                'label'    => 'Consultar Expediente',
            ],
            [
                'keywords' => ['habilitacion', 'comercio', 'local comercial', 'negocio', 'apertura', 'licencia', 'ordenanza'],
                'texto'    => 'Encontrá información sobre habilitaciones comerciales, formularios y ordenanzas.',
                'url'      => route('habilitaciones.index'),
                'label'    => 'Ver Habilitaciones',
            ],
            [
                'keywords' => ['turismo', 'turistico', 'visitar', 'atractivo', 'gastronomia', 'restaurante', 'evento', 'rawson', 'ohiggins', 'castilla', 'cucha'],
                'texto'    => 'Explorá los atractivos turísticos de Chacabuco y sus delegaciones.',
                'url'      => route('turismo.index'),
                'label'    => 'Ver Turismo',
            ],
            [
                'keywords' => ['noticia', 'noticias', 'novedad', 'novedades'],
                'texto'    => 'Consultá las últimas noticias del municipio.',
                'url'      => route('noticias.index'),
                'label'    => 'Ver Noticias',
            ],
            [
                'keywords' => ['omic', 'consumidor', 'denunciar comercio', 'defensa del consumidor', 'derechos'],
                'texto'    => 'La OMIC atiende consultas y denuncias sobre derechos del consumidor.',
                'url'      => route('omic.index'),
                'label'    => 'Ir a OMIC',
            ],
            [
                'keywords' => ['telefono', 'telefonos', 'numero', 'numeros', 'directorio', 'llamar', 'contacto'],
                'texto'    => 'Encontrá el directorio completo de teléfonos útiles del municipio.',
                'url'      => route('telefonos-utiles.index'),
                'label'    => 'Ver Teléfonos Útiles',
            ],
            [
                'keywords' => ['tramite', 'tramites', 'servicio', 'servicios', 'gestion', 'infraccion', 'multa'],
                'texto'    => 'Accedé a todos los trámites y servicios municipales.',
                'url'      => route('tramites-servicios.index'),
                'label'    => 'Ver Trámites y Servicios',
            ],
            [
                'keywords' => ['gobierno abierto', 'licitacion', 'licitaciones', 'gasto', 'gastos', 'presupuesto', 'compra', 'compras', 'contratacion', 'transparencia'],
                'texto'    => 'En Gobierno Abierto encontrás licitaciones, gastos y más información de transparencia municipal.',
                'url'      => route('gobierno-abierto.index'),
                'label'    => 'Ver Gobierno Abierto',
            ],
            [
                'keywords' => ['proveedor', 'proveedores', 'empresa proveedora', 'registro de proveedores'],
                'texto'    => 'Consultá el registro de proveedores del municipio.',
                'url'      => route('proveedores.index'),
                'label'    => 'Ver Proveedores',
            ],
        ];

        foreach ($secciones as $seccion) {
            foreach ($seccion['keywords'] as $kw) {
                if (str_contains($termino, $kw)) {
                    return $seccion;
                }
            }
        }

        return null;
    }
}
