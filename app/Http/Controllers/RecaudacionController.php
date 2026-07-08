<?php

namespace App\Http\Controllers;

use App\Models\RecaudacionDocumento;
use App\Models\RecaudacionTramiteOnline;

class RecaudacionController extends Controller
{
    public function index()
    {
        $documentos     = RecaudacionDocumento::orderBy('orden')->orderBy('id')->activos()->get();
        $tramiteOnline  = RecaudacionTramiteOnline::instancia();

        return view('recaudacion.index', compact('documentos', 'tramiteOnline'));
    }
}
