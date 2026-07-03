<?php

namespace App\Http\Controllers;

use App\Models\ObraAnexo;
use App\Models\ObraConfiguracion;
use App\Models\ObraNormativa;
use App\Models\ObraProcedimiento;

class ObrasParticularesController extends Controller
{
    public function index()
    {
        $normativasObras    = ObraNormativa::visible()->seccion('obras')->orderBy('orden')->orderBy('nombre')->get();
        $normativasBalcones = ObraNormativa::visible()->seccion('balcones')->orderBy('orden')->orderBy('nombre')->get();
        $anexos             = ObraAnexo::orderBy('orden')->orderBy('nombre')->get();

        $procedimientosObras = ObraProcedimiento::visible()
            ->seccion('obras')
            ->where('codigo', '!=', 'notas')
            ->orderBy('orden')
            ->get();

        $notasObras = ObraProcedimiento::visible()
            ->seccion('obras')
            ->where('codigo', 'notas')
            ->first();

        $procedimientosBalcones = ObraProcedimiento::visible()
            ->seccion('balcones')
            ->orderBy('orden')
            ->get();

        $procedimientosMensura = ObraProcedimiento::visible()
            ->seccion('mensura')
            ->orderBy('orden')
            ->get();

        $libreDeuda = ObraProcedimiento::visible()
            ->seccion('libre_deuda')
            ->first();

        $config = ObraConfiguracion::instancia();

        return view('obras-particulares.index', compact(
            'normativasObras', 'normativasBalcones', 'anexos',
            'procedimientosObras', 'notasObras',
            'procedimientosBalcones', 'procedimientosMensura', 'libreDeuda',
            'config'
        ));
    }
}
