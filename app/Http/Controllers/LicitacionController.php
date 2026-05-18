<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use Illuminate\Http\Request;

class LicitacionController extends Controller
{
    public function index(Request $request)
    {
        $tipo = in_array($request->get('tipo'), ['publica', 'privada'], true)
            ? $request->get('tipo')
            : null;
        $busqueda = trim((string) $request->get('q'));

        $query = Licitacion::query();

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

        $ultimasLicitaciones = Licitacion::query()
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $licitaciones = $query
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends($request->query());

        return view('gobierno-abierto.licitaciones.index', [
            'licitaciones' => $licitaciones,
            'ultimasLicitaciones' => $ultimasLicitaciones,
            'tipo' => $tipo,
            'busqueda' => $busqueda,
            'totalResultados' => $licitaciones->total(),
        ]);
    }
}
