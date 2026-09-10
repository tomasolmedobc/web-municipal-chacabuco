<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function index(): Response
    {
        $noticias = Noticia::where('estado', 'publicado')
            ->orderByDesc('fecha')
            ->limit(30)
            ->get();

        $content = view('noticias.feed', compact('noticias'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
