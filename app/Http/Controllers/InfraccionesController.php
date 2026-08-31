<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InfraccionesController extends Controller
{
    public function index()
    {
        return view('infracciones.index', [
            'criterio' => session('infracciones_criterio', 'dominio'),
            'valor' => session('infracciones_valor', ''),
            'infracciones' => $this->resultadosDesdeSesion('infracciones_resultados'),
            'cedulas' => $this->resultadosDesdeSesion('infracciones_cedulas'),
            'consultado' => session('infracciones_consultado', false),
            'mensaje' => session('infracciones_mensaje'),
        ]);
    }

    public function consultar(Request $request)
    {
        $data = $request->validate([
            'criterio' => ['required', 'in:dominio,dni'],
            'valor' => ['required', 'string', 'max:30'],
        ], [
            'criterio.required' => 'Seleccione un criterio de busqueda.',
            'valor.required' => 'Ingrese el dato para consultar.',
        ]);

        $criterio = $data['criterio'];
        $valor = trim((string) $data['valor']);

        try {
            $infracciones = $this->buscarInfracciones($criterio, $valor);

            $cedulas = $criterio === 'dni' ? $this->buscarCedulas($valor)->all() : [];

            return redirect()
                ->route('infracciones.index')
                ->with([
                    'infracciones_criterio'    => $criterio,
                    'infracciones_valor'       => $valor,
                    'infracciones_resultados'  => $infracciones->all(),
                    'infracciones_cedulas'     => $cedulas,
                    'infracciones_cedulas_ids' => collect($cedulas)->pluck('id_falta')->map(fn ($id) => (int) $id)->toArray(),
                    'infracciones_consultado'  => true,
                    'infracciones_mensaje'     => null,
                ]);
        } catch (QueryException $exception) {
            return redirect()
                ->route('infracciones.index')
                ->with([
                    'infracciones_criterio' => $criterio,
                    'infracciones_valor' => $valor,
                    'infracciones_resultados' => [],
                    'infracciones_cedulas' => [],
                    'infracciones_consultado' => true,
                    'infracciones_mensaje' => $this->mensajeErrorConsulta($exception),
                ]);
        }
    }

    public function cedula(int $falta): Response
    {
        abort_unless(in_array($falta, session('infracciones_cedulas_ids', []), true), 403);

        $cedula = DB::connection('juzgado')
            ->table('faltas as f')
            ->leftJoin('infractores as i', 'i.id_infractor', '=', 'f.id_infractor')
            ->leftJoin('localidades as l', 'l.id_localidad', '=', 'i.id_localidad')
            ->leftJoin('cedulas as c', 'c.id_cedula', '=', 'f.id_cedula')
            ->select([
                'i.nombre_completo',
                'i.dni',
                'i.domicilio',
                'l.descripcion as localidad',
                'f.id_disposicion',
                'f.causa',
                'f.acta',
                DB::raw("DATE_FORMAT(c.fecha, '%e/%c/%Y') as fecha_cedula"),
                'c.horario',
            ])
            ->where('f.id_falta', $falta)
            ->first();

        abort_if(! $cedula, 404);

        return response($this->renderPdf('infracciones.pdf.cedula', [
            'cedula' => $cedula,
            'logoPath' => $this->logoPdfPath(),
        ]), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $this->nombreArchivo('Cedula', $cedula->nombre_completo ?: 'infraccion') . '"',
        ]);
    }

    public function libreDeuda(Request $request): Response
    {
        $data = $request->validate([
            'dni' => ['required', 'digits_between:7,8'],
            'apellido' => ['required', 'string', 'max:80'],
            'nombre' => ['required', 'string', 'max:80'],
        ]);

        $infracciones = $this->buscarInfracciones('dni', $data['dni']);

        abort_if($infracciones->isNotEmpty(), 422, 'No se puede emitir libre deuda porque el DNI registra infracciones.');

        DB::connection('juzgado')
            ->table('libres_deudas')
            ->insert([
                'dni' => $data['dni'],
                'apellido' => mb_strtoupper($data['apellido']),
                'nombre' => mb_strtoupper($data['nombre']),
            ]);

        return response($this->renderPdf('infracciones.pdf.libre-deuda', [
            'data' => $data,
            'nombreCompleto' => mb_strtoupper($data['nombre'] . ' ' . $data['apellido']),
            'logoPath' => $this->logoPdfPath(),
        ]), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $this->nombreArchivo('Libre Deuda', $data['nombre'] . ' ' . $data['apellido']) . '"',
        ]);
    }

    private function buscarInfracciones(string $criterio, string $valor)
    {
        $query = DB::connection('juzgado')
            ->table('faltas as f')
            ->leftJoin('infractores as i', 'i.id_infractor', '=', 'f.id_infractor')
            ->leftJoin('calles as c', 'c.id_calle', '=', 'f.id_calle')
            ->select([
                DB::raw("DATE_FORMAT(f.fecha, '%e/%c/%Y') as fecha"),
                'f.patente',
                'f.vehiculo',
                'c.descripcion as calle',
                'f.altura',
                'f.causa',
                'f.acta',
                'i.nombre_completo',
            ])
            ->where('f.id_estado', 1)
            ->where('f.id_origen', 1)
            ->whereRaw('f.fecha BETWEEN DATE_SUB(CURDATE(), INTERVAL 5 YEAR) AND CURDATE()');

        if ($criterio === 'dominio') {
            $query->where(function ($query) use ($valor) {
                $query->where('f.patente', $valor)
                    ->orWhere('f.vehiculo', 'like', "%{$valor}%");
            });
        } else {
            $query->where('i.dni', $valor);
        }

        return $query
            ->orderByDesc('f.fecha')
            ->get();
    }

    private function resultadosDesdeSesion(string $clave)
    {
        return collect(session($clave, []))
            ->map(fn ($item) => is_array($item) ? (object) $item : $item);
    }

    private function buscarCedulas(string $dni)
    {
        return DB::connection('juzgado')
            ->table('cedulas as c')
            ->leftJoin('faltas as f', 'f.id_cedula', '=', 'c.id_cedula')
            ->leftJoin('infractores as i', 'i.id_infractor', '=', 'f.id_infractor')
            ->select([
                'f.id_falta',
                DB::raw("DATE_FORMAT(f.fecha, '%e/%c/%Y') as fecha"),
                DB::raw("DATE_FORMAT(f.fecha_notificacion, '%e/%c/%Y') as notificacion"),
                'f.causa',
                'f.acta',
            ])
            ->where('f.id_estado', 1)
            ->where('i.dni', $dni)
            ->orderByDesc('f.fecha')
            ->get();
    }

    private function renderPdf(string $view, array $data): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view($view, $data)->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->output();
    }

    private function logoPdfPath(): string
    {
        $path = public_path('images/infracciones/chacabuco.jpg');

        if (! file_exists($path)) {
            return '';
        }

        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
    }

    private function nombreArchivo(string $prefijo, string $nombre): string
    {
        $limpio = preg_replace('/[^A-Za-z0-9 _-]/', '', $nombre) ?: 'documento';

        return trim($prefijo . ' - ' . $limpio) . '.pdf';
    }

    private function mensajeErrorConsulta(QueryException $exception): string
    {
        $mensaje = $exception->getMessage();

        Log::error('Error al consultar infracciones.', [
            'connection' => 'juzgado',
            'message' => $mensaje,
        ]);

        if (app()->environment('local')) {
            return 'Error local de consulta: ' . $mensaje;
        }

        if (str_contains($mensaje, '[2002]') || str_contains($mensaje, 'Connection refused') || str_contains($mensaje, 'deneg')) {
            return 'No se pudo conectar con la base del Juzgado de Faltas. Revise host, puerto, usuario, clave o permisos de conexion externa.';
        }

        return 'No se pudo realizar la consulta en este momento. Intente nuevamente mas tarde.';
    }
}
