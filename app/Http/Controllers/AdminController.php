<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $busqueda = $request->get('q');

        $query = Noticia::query();

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                  ->orWhere('contenido', 'like', '%' . $busqueda . '%')
                  ->orWhere('slug', 'like', '%' . $busqueda . '%')
                  ->orWhere('autor', 'like', '%' . $busqueda . '%');
            });
        }

        $noticias = $query
            ->orderBy('fecha', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.dashboard', compact('noticias', 'busqueda'));
    }
}