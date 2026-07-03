<?php

namespace App\Http\Controllers;

use App\Models\TasasConfiguracion;
use App\Models\TasasGrupo;

class TasasMunicipalesController extends Controller
{
    public function index()
    {
        $config = TasasConfiguracion::instancia();

        $grupos = TasasGrupo::visible()
            ->orderBy('orden')
            ->with(['cuotasVisibles' => fn($q) => $q->orderBy('orden')])
            ->get();

        return view('tasas.index', compact('config', 'grupos'));
    }
}
