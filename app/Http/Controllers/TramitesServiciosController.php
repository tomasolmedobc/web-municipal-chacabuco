<?php

namespace App\Http\Controllers;

class TramitesServiciosController extends Controller
{
    public function index()
    {
        return view('tramites-servicios.index', [
            'tramites' => collect(config('tramites_servicios.tramites', [])),
            'servicios' => collect(config('tramites_servicios.servicios', [])),
        ]);
    }
}
