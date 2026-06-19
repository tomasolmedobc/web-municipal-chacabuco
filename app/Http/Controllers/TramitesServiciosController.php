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

    public function saludMental()
    {
        $salones = collect(config('salud_mental.salones', []))
            ->sortByDesc(fn ($salon) => array_sum(array_map('count', $salon['agenda'])))
            ->values()
            ->all();

        return view('tramites-servicios.salud-mental', compact('salones'));
    }
}
