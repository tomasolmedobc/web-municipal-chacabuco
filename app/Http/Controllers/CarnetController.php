<?php

namespace App\Http\Controllers;

use App\Models\CarnetConfiguracion;
use App\Models\CarnetMaterial;

class CarnetController extends Controller
{
    public function index()
    {
        $config    = CarnetConfiguracion::instancia();
        $materiales = CarnetMaterial::orderBy('orden')->orderBy('id')->activos()->get();

        return view('carnet.index', compact('config', 'materiales'));
    }
}
