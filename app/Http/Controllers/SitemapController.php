<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Noticia;
use App\Models\TurismoItem;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $noticias = Noticia::where('estado', 'publicado')
            ->select('slug', 'updated_at')
            ->orderByDesc('fecha')
            ->get();

        $localidades = Localidad::visible()
            ->select('slug', 'updated_at')
            ->orderBy('orden')
            ->get();

        $items = TurismoItem::visible()
            ->where('mostrar_detalle', true)
            ->with('localidad:id,slug')
            ->select('id', 'localidad_id', 'updated_at')
            ->get();

        $content = view('sitemap', compact('noticias', 'localidades', 'items'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
