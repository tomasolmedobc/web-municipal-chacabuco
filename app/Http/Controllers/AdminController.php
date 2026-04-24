<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Noticia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $busqueda = $request->get('q');

        $query = Noticia::with('categorias');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', "%{$busqueda}%")
                    ->orWhere('contenido', 'like', "%{$busqueda}%")
                    ->orWhere('slug', 'like', "%{$busqueda}%")
                    ->orWhere('autor', 'like', "%{$busqueda}%");
            });
        }

        $noticias = $query
            ->orderBy('fecha', 'desc')
            ->paginate(15)
            ->appends($request->query());

        $stats = [
            'noticias_total' => Noticia::count(),
            'noticias_publicadas' => Noticia::where('estado', 'publicado')->count(),
            'noticias_ocultas' => Noticia::where('estado', 'oculto')->count(),
            'usuarios_total' => User::count(),
        ];

        $ultimasNoticias = Noticia::with('categorias')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'noticias',
            'busqueda',
            'stats',
            'ultimasNoticias'
        ));
    }
}