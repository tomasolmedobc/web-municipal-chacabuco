<?php

namespace App\Http\Controllers;

use App\Models\ObraAnexo;
use App\Models\ObraCategoria;
use App\Models\ObraConfiguracion;

class ObrasParticularesController extends Controller
{
    public function index()
    {
        $categorias = ObraCategoria::visible()
            ->with([
                'normativas'     => fn($q) => $q->visible()->orderBy('orden')->orderBy('nombre'),
                'procedimientos' => fn($q) => $q->visible()->orderBy('orden'),
            ])
            ->orderBy('orden')
            ->get();

        $anexos = ObraAnexo::orderBy('orden')->orderBy('nombre')->get();
        $config = ObraConfiguracion::instancia();

        return view('obras-particulares.index', compact('categorias', 'anexos', 'config'));
    }
}
