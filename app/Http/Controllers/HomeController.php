<?php

namespace App\Http\Controllers;

use App\Models\Noticia;

class HomeController extends Controller
{
    public function index()
    {
        $ultimasNoticias = Noticia::orderBy('fecha', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('ultimasNoticias'));
    }
}