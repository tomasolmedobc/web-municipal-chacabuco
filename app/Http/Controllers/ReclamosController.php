<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReclamosController extends Controller
{
    public function index()
    {
        return view('reclamos.index', [
            'areas' => $this->areas(),
            'tipos' => $this->tipos(),
            'calles' => $this->calles(),
            'codigoCreado' => session('reclamos_codigo_creado'),
            'consultaCodigo' => session('reclamos_consulta_codigo', ''),
            'reclamo' => $this->resultadoDesdeSesion('reclamos_resultado'),
            'pases' => $this->resultadosDesdeSesion('reclamos_pases'),
            'mensaje' => session('reclamos_mensaje'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'apellido' => ['required', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:50'],
            'telefono' => ['required', 'digits_between:6,10'],
            'dni' => ['required', 'digits_between:7,9'],
            'area' => ['required', 'integer'],
            'tipo' => ['required', 'integer'],
            'calle' => ['nullable', 'integer'],
            'altura' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'detalles' => ['required', 'string', 'max:3000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        try {
            $codigo = DB::connection('reclamos')->transaction(function () use ($data, $request) {
                $imagen = null;

                if ($request->hasFile('imagen')) {
                    $imagen = basename($request->file('imagen')->store('reclamos', 'public'));
                }

                return DB::connection('reclamos')
                    ->table('reclamos')
                    ->insertGetId([
                        'fecha_hora' => now(),
                        'id_area' => $data['area'],
                        'id_tipo_reclamo' => $data['tipo'],
                        'apellido' => mb_strtoupper($data['apellido']),
                        'nombre' => mb_strtoupper($data['nombre']),
                        'dni' => $data['dni'],
                        'id_calle' => (int) ($data['calle'] ?? 0),
                        'altura' => (int) ($data['altura'] ?? 0),
                        'id_zona' => $this->zonaPara((int) ($data['calle'] ?? 0), (int) ($data['altura'] ?? 0)),
                        'telefono' => $data['telefono'],
                        'observaciones' => $data['detalles'],
                        'id_estado' => 1,
                        'image' => $imagen,
                        'id_origen' => 3,
                        'autor' => trim($data['apellido'] . ' ' . $data['nombre']),
                        'id_categoria' => 1,
                    ]);
            });

            return redirect()
                ->route('reclamos.index')
                ->with('reclamos_codigo_creado', $codigo);
        } catch (QueryException $exception) {
            return redirect()
                ->route('reclamos.index')
                ->withInput()
                ->with('reclamos_mensaje', $this->mensajeError($exception));
        }
    }

    public function consultar(Request $request)
    {
        $data = $request->validate([
            'idreclamo' => ['required', 'integer', 'min:1'],
        ], [
            'idreclamo.required' => 'Ingrese el codigo de reclamo.',
            'idreclamo.integer' => 'El codigo solo puede contener numeros.',
        ]);

        $codigo = (int) $data['idreclamo'];

        try {
            $reclamo = $this->buscarReclamo($codigo);

            return redirect()
                ->route('reclamos.index')
                ->with([
                    'reclamos_consulta_codigo' => $codigo,
                    'reclamos_resultado' => $reclamo,
                    'reclamos_pases' => $reclamo ? $this->buscarPases($codigo)->all() : [],
                    'reclamos_mensaje' => $reclamo ? null : 'No hay reclamos asociados a ese codigo.',
                ]);
        } catch (QueryException $exception) {
            return redirect()
                ->route('reclamos.index')
                ->with([
                    'reclamos_consulta_codigo' => $codigo,
                    'reclamos_resultado' => null,
                    'reclamos_pases' => [],
                    'reclamos_mensaje' => $this->mensajeError($exception),
                ]);
        }
    }

    private function areas()
    {
        return DB::connection('reclamos')
            ->table('areas')
            ->orderBy('descripcion')
            ->get(['id_area', 'descripcion']);
    }

    private function tipos()
    {
        return DB::connection('reclamos')
            ->table('reclamos_tipos')
            ->orderBy('descripcion')
            ->get(['id_tipo_reclamo', 'descripcion', 'id_area']);
    }

    private function calles()
    {
        return DB::connection('reclamos')
            ->table('calles')
            ->orderBy('descripcion')
            ->get(['id_calle', 'descripcion']);
    }

    private function zonaPara(int $calle, int $altura): int
    {
        if ($calle > 0 && $altura > 0) {
            $zona = DB::connection('reclamos')
                ->table('calles_zonas')
                ->where('id_calle', $calle)
                ->where('altura_min', '<=', $altura)
                ->where('altura_max', '>', $altura)
                ->value('id_zona');

            if ($zona) {
                return (int) $zona;
            }
        }

        return (int) DB::connection('reclamos')
            ->table('zonas')
            ->where('descripcion', 'OTRA')
            ->value('id_zona');
    }

    private function buscarReclamo(int $codigo): ?object
    {
        return DB::connection('reclamos')
            ->table('reclamos as r')
            ->leftJoin('areas as a', 'a.id_area', '=', 'r.id_area')
            ->leftJoin('reclamos_tipos as rt', 'rt.id_tipo_reclamo', '=', 'r.id_tipo_reclamo')
            ->leftJoin('estados as e', 'e.id_estado', '=', 'r.id_estado')
            ->select([
                DB::raw("DATE_FORMAT(r.fecha_hora, '%e/%c/%Y') AS fecha"),
                'a.descripcion as area',
                'rt.descripcion as tipo',
                'r.apellido',
                'r.nombre',
                'e.estado',
                'r.observaciones',
            ])
            ->where('r.id_reclamo', $codigo)
            ->first();
    }

    private function buscarPases(int $codigo)
    {
        return DB::connection('reclamos')
            ->table('reclamos_pases as p')
            ->leftJoin('areas as a', 'a.id_area', '=', 'p.id_area_nueva')
            ->select([
                DB::raw("DATE_FORMAT(p.fecha, '%e/%c/%Y') AS fecha"),
                'a.descripcion as area',
                'p.observaciones',
            ])
            ->where('p.id_reclamo', $codigo)
            ->orderBy('p.fecha')
            ->get();
    }

    private function resultadoDesdeSesion(string $clave): ?object
    {
        $resultado = session($clave);

        if (! $resultado) {
            return null;
        }

        return is_array($resultado) ? (object) $resultado : $resultado;
    }

    private function resultadosDesdeSesion(string $clave)
    {
        return collect(session($clave, []))
            ->map(fn ($item) => is_array($item) ? (object) $item : $item);
    }

    private function mensajeError(QueryException $exception): string
    {
        Log::error('Error al consultar o cargar reclamos.', [
            'connection' => 'reclamos',
            'message' => $exception->getMessage(),
        ]);

        if (app()->environment('local')) {
            return 'Error local de reclamos: ' . $exception->getMessage();
        }

        return 'No se pudo realizar la operacion en este momento. Intente nuevamente mas tarde.';
    }
}
