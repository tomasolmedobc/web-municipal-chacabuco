<?php

namespace App\Http\Controllers;

use App\Services\PublicAccessButtonService;

class TramitesServiciosController extends Controller
{
    public function index(PublicAccessButtonService $buttons)
    {
        return view('tramites-servicios.index', [
            'tramites' => $buttons->tramitesVisibles(),
            'servicios' => $buttons->serviciosVisibles(),
        ]);
    }
}
