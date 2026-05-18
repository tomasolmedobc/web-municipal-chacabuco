<?php

namespace App\Http\Controllers;

use App\Models\Noticia;

class HomeController extends Controller
{
    public function index()
    {
        $noticiaDestacada = Noticia::with('categorias')
            ->where('estado', 'publicado')
            ->where('destacada', true)
            ->where(function ($q) {
                $q->whereNull('destacada_hasta')
                    ->orWhere('destacada_hasta', '>=', now());
            })
            ->orderBy('destacada_hasta', 'desc')
            ->orderBy('fecha', 'desc')
            ->first();

        $ultimasNoticias = Noticia::where('estado', 'publicado')
            ->when($noticiaDestacada, function ($query) use ($noticiaDestacada) {
                $query->where('id', '!=', $noticiaDestacada->id);
            })
            ->orderBy('fecha', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('noticiaDestacada', 'ultimasNoticias'));
    }
}
