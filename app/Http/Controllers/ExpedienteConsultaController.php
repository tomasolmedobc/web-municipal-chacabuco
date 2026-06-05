<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpedienteConsultaController extends Controller
{
    public function index()
    {
        return view('expedientes.index', [
            'form' => session('expedientes_form', [
                'numero' => '',
                'letra' => '',
                'anio' => now()->year,
            ]),
            'expediente' => $this->resultadoDesdeSesion('expedientes_resultado'),
            'pasos' => $this->resultadosDesdeSesion('expedientes_pasos'),
            'mensaje' => session('expedientes_mensaje'),
        ]);
    }

    public function consultar(Request $request)
    {
        $validated = $request->validate([
            'numero' => ['required', 'integer', 'min:1'],
            'letra' => ['nullable', 'alpha', 'max:3'],
            'anio' => ['required', 'integer', 'between:2000,2100'],
        ], [
            'numero.required' => 'Debe ingresar el numero del expediente.',
            'numero.integer' => 'El numero del expediente solo puede contener numeros.',
            'letra.alpha' => 'La letra del expediente solo puede contener letras.',
            'letra.max' => 'La letra del expediente no puede superar los 3 caracteres.',
            'anio.required' => 'Debe ingresar el anio del expediente.',
            'anio.integer' => 'El anio del expediente solo puede contener numeros.',
            'anio.between' => 'El anio ingresado no es valido.',
        ]);

        $form = [
            'numero' => (int) $validated['numero'],
            'letra' => strtoupper((string) ($validated['letra'] ?? '')),
            'anio' => (int) $validated['anio'],
        ];

        try {
            $expediente = $this->buscarExpediente($form);

            if (! $expediente) {
                return redirect()
                    ->route('expedientes.index')
                    ->with([
                        'expedientes_form' => $form,
                        'expedientes_resultado' => null,
                        'expedientes_pasos' => [],
                        'expedientes_mensaje' => 'No se encontraron expedientes con ese codigo.',
                    ]);
            }

            return redirect()
                ->route('expedientes.index')
                ->with([
                    'expedientes_form' => $form,
                    'expedientes_resultado' => $expediente,
                    'expedientes_pasos' => $this->buscarPasos($expediente)->all(),
                    'expedientes_mensaje' => null,
                ]);
        } catch (QueryException $exception) {
            return redirect()
                ->route('expedientes.index')
                ->with([
                    'expedientes_form' => $form,
                    'expedientes_resultado' => null,
                    'expedientes_pasos' => [],
                    'expedientes_mensaje' => $this->mensajeErrorConsulta($exception),
                ]);
        }
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

    private function mensajeErrorConsulta(QueryException $exception): string
    {
        $mensaje = $exception->getMessage();

        if (str_contains($mensaje, '[2002]') || str_contains($mensaje, 'Connection refused') || str_contains($mensaje, 'deneg')) {
            return 'No se pudo conectar con la base de expedientes. Revise host, puerto, usuario, clave o si el servidor permite conexiones externas.';
        }

        return 'No se pudo realizar la consulta en este momento. Intente nuevamente mas tarde.';
    }

    private function buscarExpediente(array $form): ?object
    {
        $query = DB::connection('expedientes')
            ->table('expedientes as e')
            ->leftJoin('tramites as t', 't.IdTramite', '=', 'e.IdTramite')
            ->select([
                'e.IdExpediente',
                't.IdTramite',
                't.Descripcion',
                'e.Detalle',
                'e.Nombres',
                'e.Apellidos',
                'e.RazonSocial',
                'e.Estado',
                'e.FechaHoraIngreso',
                'e.CodExpediente',
                'e.ObservacionPub',
                'e.MotivoAnulacion',
                'e.Anio',
                'e.Letra',
                'e.NumExpediente',
            ])
            ->where('e.NumExpediente', $form['numero'])
            ->where('e.Anio', $form['anio'])
            ->where('e.Visibilidad', 'PUBL')
            ->where('e.Estado', '>', 0);

        if ($form['letra'] !== '') {
            $query->where('e.Letra', $form['letra']);
        } else {
            $query->where(function ($query) {
                $query->whereNull('e.Letra')
                    ->orWhere('e.Letra', '');
            });
        }

        return $query->first();
    }

    private function buscarPasos(object $expediente)
    {
        return DB::connection('expedientes')
            ->table('expedientespasos as ep')
            ->leftJoin('oficinas as o', 'o.IdOficina', '=', 'ep.IdOficina')
            ->select([
                'ep.FechaHoraIngreso',
                'ep.ObservacionPub',
                'o.Nombre',
            ])
            ->where('ep.IdExpediente', trim((string) $expediente->IdExpediente))
            ->where('ep.IdTramite', trim((string) $expediente->IdTramite))
            ->orderBy('ep.FechaHoraIngreso')
            ->get();
    }

    public static function estado(int|string|null $estado): string
    {
        return match ((int) $estado) {
            0 => 'Anulado',
            1 => 'Pendiente',
            2 => 'Terminado',
            3 => 'Extraviado',
            default => 'No definido',
        };
    }

    public static function fecha(?string $fecha): string
    {
        if (! $fecha) {
            return '-';
        }

        return Carbon::parse($fecha)->format('d/m/Y H:i');
    }
}
