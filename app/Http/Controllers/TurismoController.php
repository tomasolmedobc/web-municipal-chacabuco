<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\TurismoItem;
use Illuminate\Http\Request;

class TurismoController extends Controller
{
    public function eventosProximos()
    {
        $eventos = TurismoItem::visible()
            ->where('tipo', TurismoItem::TIPO_EVENTO)
            ->where(function ($q) {
                $q->where('fecha_fin', '>=', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('fecha_fin')
                         ->where('fecha_inicio', '>=', now()->toDateString());
                  });
            })
            ->with('localidad')
            ->orderBy('fecha_inicio')
            ->limit(3)
            ->get()
            ->map(fn ($e) => [
                'titulo'         => $e->titulo,
                'localidad'      => $e->localidad?->nombre,
                'slug_localidad' => $e->localidad?->slug,
                'fecha_inicio'   => $e->fecha_inicio?->format('d/m/Y'),
                'fecha_fin'      => $e->fecha_fin?->format('d/m/Y'),
                'hora_inicio'    => $e->hora_inicio ? substr($e->hora_inicio, 0, 5) : null,
            ]);

        return response()->json($eventos);
    }

    public function index()
    {
        $localidades = Localidad::visible()->orderBy('orden')->get();

        $hoy = now()->toDateString();

        $destacados = TurismoItem::visible()
            ->where('destacado', true)
            ->where(function ($q) use ($hoy) {
                // Atractivos y gastronomía: siempre se muestran
                $q->where('tipo', '!=', TurismoItem::TIPO_EVENTO)
                  // Eventos: solo los que aún no finalizaron
                  ->orWhere(function ($q2) use ($hoy) {
                      $q2->where('tipo', TurismoItem::TIPO_EVENTO)
                         ->where(function ($q3) use ($hoy) {
                             $q3->where('fecha_fin', '>=', $hoy)
                                ->orWhere(function ($q4) use ($hoy) {
                                    $q4->whereNull('fecha_fin')
                                       ->where('fecha_inicio', '>=', $hoy);
                                });
                         });
                  });
            })
            ->with('localidad')
            ->orderBy('orden')
            ->limit(8)
            ->get();

        $eventosFinalizados = TurismoItem::visible()
            ->where('tipo', TurismoItem::TIPO_EVENTO)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('fecha_fin')
                       ->where('fecha_fin', '<', now()->toDateString());
                })->orWhere(function ($q2) {
                    $q2->whereNull('fecha_fin')
                       ->where('fecha_inicio', '<', now()->toDateString());
                });
            })
            ->with('localidad')
            ->orderByDesc('fecha_inicio')
            ->limit(12)
            ->get();

        return view('turismo.index', [
            'localidades' => $localidades,
            'destacados' => $destacados,
            'eventosFinalizados' => $eventosFinalizados,
        ]);
    }

    public function show(Request $request, string $localidad)
    {
        $localidad = Localidad::visible()->where('slug', $localidad)->firstOrFail();

        $tipo = $request->filled('tipo') ? TurismoItem::normalizarTipo($request->get('tipo')) : null;

        $items = TurismoItem::visible()
            ->where('localidad_id', $localidad->id)
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get()
            ->groupBy('tipo');

        $localidad->historia = $this->limpiarHtml($localidad->historia);

        return view('turismo.show', [
            'localidad' => $localidad,
            'items' => $items,
            'tipoActivo' => $tipo,
            'tipos' => TurismoItem::TIPOS,
        ]);
    }

    public function showItem(string $localidad, int $item)
    {
        $localidad = Localidad::visible()->where('slug', $localidad)->firstOrFail();

        $item = TurismoItem::visible()
            ->where('localidad_id', $localidad->id)
            ->where('mostrar_detalle', true)
            ->with('galeria', 'archivos')
            ->findOrFail($item);

        $config = TurismoItem::configTipo($item->tipo);
        $item->descripcion = $this->limpiarHtml($item->descripcion);

        return view('turismo.item', compact('localidad', 'item', 'config'));
    }

    private function limpiarHtml(?string $html): ?string
    {
        if (! $html) {
            return $html;
        }

        // Elimina atributos data-*, class e id inyectados por Google Translate u otras extensiones
        $html = preg_replace('/\s(data-[a-z\-]+=[""][^""]*[""])/i', '', $html);
        $html = preg_replace('/\s(class|id)=[""][^""]*[""]/i', '', $html);

        // Elimina párrafos vacíos o con solo &nbsp; que TinyMCE genera al presionar Enter
        $html = preg_replace('/<p>(\s|&nbsp;)*<\/p>/i', '', $html);

        return trim($html);
    }
}
