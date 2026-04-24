<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Categoria;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(Request $request)
    {
        $paginaActual = (int) $request->get('page', 1);

        $busqueda = trim((string) $request->get('q'));
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $orden = $request->get('orden', 'nuevas');
        $categoriaSlug = $request->get('categoria');

        $categoriasFiltro = Categoria::whereNull('parent_id')
            ->with('hijas')
            ->orderBy('nombre')
            ->get();

        $query = Noticia::with('categorias')
            ->where('estado', 'publicado');

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                ->orWhere('contenido', 'like', '%' . $busqueda . '%')
                ->orWhere('autor', 'like', '%' . $busqueda . '%');
            });
        }

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        if ($categoriaSlug) {
            $query->whereHas('categorias', function ($q) use ($categoriaSlug) {
                $q->where('slug', $categoriaSlug);
            });
        }

        $destacada = null;

        if ($paginaActual === 1 && $busqueda === '' && !$desde && !$hasta && !$categoriaSlug) {
            $destacada = Noticia::with('categorias')
                ->where('estado', 'publicado')
                ->whereNotNull('imagen_destacada')
                ->where('imagen_destacada', '!=', '')
                ->orderBy('fecha', 'desc')
                ->first();
        }

        if ($destacada) {
            $query->where('id', '!=', $destacada->id);
        }

        if ($orden === 'antiguas') {
            $query->orderBy('fecha', 'asc');
        } else {
            $query->orderBy('fecha', 'desc');
        }

        $noticias = $query
            ->paginate(9)
            ->appends($request->query());

        $totalResultados = $noticias->total();

        return view('noticias.index', compact(
            'destacada',
            'noticias',
            'busqueda',
            'desde',
            'hasta',
            'orden',
            'categoriaSlug',
            'categoriasFiltro',
            'totalResultados'
        ));
    }

    public function show($slug)
    {
        $query = Noticia::with(['archivos', 'categorias'])
            ->where('slug', $slug);

        if (!auth()->check() || !in_array(auth()->user()->rol, ['admin', 'editor'])) {
            $query->where('estado', 'publicado');
        }

        $noticia = $query->firstOrFail();

        return view('noticias.show', compact('noticia'));
    }
}