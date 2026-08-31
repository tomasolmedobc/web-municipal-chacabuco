<?php

namespace App\Http\Controllers;

use App\Models\HabilitacionDocumento;
use Illuminate\Http\Request;

class HabilitacionesController extends Controller
{
    public function index(Request $request)
    {
        $contenido = config('habilitaciones');

        $urlPrefactibilidad = config_sistema('hab_tramite_prefactibilidad_url');
        $urlHabilitacion    = config_sistema('hab_tramite_habilitacion_url');

        if ($urlPrefactibilidad || $urlHabilitacion) {
            $tramites = $contenido['tramites_online'];
            if ($urlPrefactibilidad) {
                $tramites[0]['url'] = $urlPrefactibilidad;
            }
            if ($urlHabilitacion) {
                $tramites[1]['url'] = $urlHabilitacion;
            }
            $contenido['tramites_online'] = $tramites;
        }

        $busquedaOrdenanzas = trim((string) $request->get('ordenanza'));

        $archivosAdmin = HabilitacionDocumento::visible()
            ->seccion(HabilitacionDocumento::SECCION_ARCHIVOS)
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get();

        $ordenanzasAdmin = HabilitacionDocumento::visible()
            ->seccion(HabilitacionDocumento::SECCION_ORDENANZAS)
            ->when($busquedaOrdenanzas, function ($query) use ($busquedaOrdenanzas) {
                $query->where(function ($query) use ($busquedaOrdenanzas) {
                    $query->where('titulo', 'like', "%{$busquedaOrdenanzas}%")
                        ->orWhere('descripcion', 'like', "%{$busquedaOrdenanzas}%")
                        ->orWhere('categoria', 'like', "%{$busquedaOrdenanzas}%")
                        ->orWhere('numero', 'like', "%{$busquedaOrdenanzas}%")
                        ->orWhere('anio', 'like', "%{$busquedaOrdenanzas}%");
                });
            })
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get();

        return view('habilitaciones.index', [
            'contenido' => $contenido,
            'archivosAdmin' => $archivosAdmin,
            'ordenanzasAdmin' => $ordenanzasAdmin,
            'busquedaOrdenanzas' => $busquedaOrdenanzas,
        ]);
    }
}
