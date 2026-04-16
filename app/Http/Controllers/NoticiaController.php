<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $paginaActual = (int) $request->get('page', 1);

        $busqueda = $request->get('q');
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        $query = Noticia::query();

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                  ->orWhere('contenido', 'like', '%' . $busqueda . '%');
            });
        }

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        $destacada = null;

        if ($paginaActual === 1 && !$busqueda && !$desde && !$hasta) {
            $destacada = Noticia::whereNotNull('imagen_destacada')
                ->where('imagen_destacada', '!=', '')
                ->orderBy('fecha', 'desc')
                ->first();
        }

        if ($destacada) {
            $query->where('id', '!=', $destacada->id);
        }

        $noticias = $query
            ->orderBy('fecha', 'desc')
            ->paginate(9)
            ->appends($request->query());

        return view('noticias.index', compact(
            'destacada',
            'noticias',
            'busqueda',
            'desde',
            'hasta'
        ));
    }

    public function show($slug)
    {
        $noticia = Noticia::where('slug', $slug)->firstOrFail();

        return view('noticias.show', compact('noticia'));
    }
}