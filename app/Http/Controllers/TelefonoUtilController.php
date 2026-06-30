<?php

namespace App\Http\Controllers;

use App\Models\TelefonoUtil;
use Illuminate\Http\Request;

class TelefonoUtilController extends Controller
{
    public function index(Request $request)
    {
        $q         = trim($request->input('q', ''));
        $categoria = $request->input('categoria', '');

        $query = TelefonoUtil::visible()
            ->orderBy('orden')
            ->orderBy('nombre');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre',    'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('email',    'like', "%{$q}%")
                    ->orWhere('direccion','like', "%{$q}%");
            });
        }

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        $telefonos    = $query->get();
        $porCategoria = $telefonos->groupBy('categoria');

        // Reordenar grupos según el orden canónico de CATEGORIAS
        $ordenado = collect(TelefonoUtil::CATEGORIAS)
            ->filter(fn ($cat) => $porCategoria->has($cat))
            ->mapWithKeys(fn ($cat) => [$cat => $porCategoria[$cat]]);

        return view('telefonos-utiles.index', [
            'porCategoria' => $ordenado,
            'categorias'   => TelefonoUtil::CATEGORIAS,
            'iconos'       => TelefonoUtil::ICONOS,
            'total'        => $telefonos->count(),
            'q'            => $q,
            'categoria'    => $categoria,
        ]);
    }
}
