<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use Illuminate\Http\Request;

class LicitacionController extends Controller
{
    public function index(Request $request)
    {
        return $this->mostrarCategoria($request, Licitacion::CATEGORIA_LICITACIONES);
    }

    public function gastosRecursosBalance(Request $request)
    {
        return $this->mostrarCategoria($request, Licitacion::CATEGORIA_GASTOS_RECURSOS_BALANCE);
    }

    public function botones(Request $request)
    {
        return $this->mostrarCategoria($request, Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS);
    }

    private function mostrarCategoria(Request $request, string $categoria)
    {
        $categoria = Licitacion::normalizarCategoria($categoria);
        $config = Licitacion::configCategoria($categoria);
        $usaFiltroTipo = $categoria === Licitacion::CATEGORIA_LICITACIONES;
        $esBotones = $categoria === Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS;

        $tipo = $usaFiltroTipo && in_array($request->get('tipo'), ['publica', 'privada'], true)
            ? $request->get('tipo')
            : null;
        $busqueda = trim((string) $request->get('q'));
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        $query = Licitacion::with('archivos')->categoria($categoria);

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                    ->orWhere('descripcion', 'like', '%' . $busqueda . '%')
                    ->orWhere('numero_expediente', 'like', '%' . $busqueda . '%');
            });
        }

        if ($desde) {
            $query->whereDate('fecha_publicacion', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha_publicacion', '<=', $hasta);
        }

        $ultimasLicitaciones = Licitacion::with('archivos')
            ->categoria($categoria)
            ->when(
                $esBotones,
                fn ($query) => $query->orderBy('orden')->orderBy('titulo'),
                fn ($query) => $query->orderByDesc('fecha_publicacion')->orderByDesc('created_at')
            )
            ->take(4)
            ->get();

        $licitaciones = $query
            ->when(
                $esBotones,
                fn ($query) => $query->orderBy('orden')->orderBy('titulo'),
                fn ($query) => $query->orderByDesc('fecha_publicacion')->orderByDesc('created_at')
            )
            ->paginate(12)
            ->appends($request->query());

        return view('gobierno-abierto.licitaciones.index', [
            'licitaciones' => $licitaciones,
            'ultimasLicitaciones' => $ultimasLicitaciones,
            'tipo' => $tipo,
            'busqueda' => $busqueda,
            'desde' => $desde,
            'hasta' => $hasta,
            'totalResultados' => $licitaciones->total(),
            'categoriaActiva' => $categoria,
            'config' => $config,
            'usaFiltroTipo' => $usaFiltroTipo,
            'esBotones' => $esBotones,
        ]);
    }
}
