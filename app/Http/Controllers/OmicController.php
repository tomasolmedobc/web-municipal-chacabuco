<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OmicController extends Controller
{
    public function index(): View
    {
        return view('omic.index', [
            'localidades' => $this->localidades(),
            'codigoCreado' => session('omic_codigo_creado'),
            'mensaje' => session('omic_mensaje'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'apellido' => ['required', 'string', 'max:80'],
            'nombre' => ['required', 'string', 'max:80'],
            'dni' => ['required', 'digits_between:7,9'],
            'nacimiento' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'telefono' => ['nullable', 'string', 'max:20'],
            'celular' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:120'],
            'direccion' => ['required', 'string', 'max:160'],
            'localidad' => ['required', 'integer'],
            'cp' => ['nullable', 'integer', 'between:1000,9999'],
            'nombre1' => ['required', 'string', 'max:160'],
            'telefono1' => ['nullable', 'string', 'max:20'],
            'direccion1' => ['nullable', 'string', 'max:160'],
            'localidad1' => ['required', 'string', 'max:120'],
            'reclamo1' => ['required', 'string', 'max:4000'],
            'pretension1' => ['required', 'string', 'max:4000'],
            'autorizado1' => ['nullable', 'in:Si,No'],
            'observaciones1' => ['nullable', 'string', 'max:4000'],
            'nombre2' => ['nullable', 'string', 'max:160'],
            'telefono2' => ['nullable', 'string', 'max:20'],
            'direccion2' => ['nullable', 'string', 'max:160'],
            'localidad2' => ['nullable', 'string', 'max:120'],
            'reclamo2' => ['nullable', 'string', 'max:4000'],
            'pretension2' => ['nullable', 'string', 'max:4000'],
            'autorizado2' => ['nullable', 'in:Si,No'],
            'observaciones2' => ['nullable', 'string', 'max:4000'],
            'nombre3' => ['nullable', 'string', 'max:160'],
            'telefono3' => ['nullable', 'string', 'max:20'],
            'direccion3' => ['nullable', 'string', 'max:160'],
            'localidad3' => ['nullable', 'string', 'max:120'],
            'reclamo3' => ['nullable', 'string', 'max:4000'],
            'pretension3' => ['nullable', 'string', 'max:4000'],
            'autorizado3' => ['nullable', 'in:Si,No'],
            'observaciones3' => ['nullable', 'string', 'max:4000'],
            'archivo1' => ['nullable', 'file', 'mimes:jpg,jpeg,png,doc,docx,pdf', 'max:10240'],
            'archivo2' => ['nullable', 'file', 'mimes:jpg,jpeg,png,doc,docx,pdf', 'max:10240'],
            'archivo3' => ['nullable', 'file', 'mimes:jpg,jpeg,png,doc,docx,pdf', 'max:10240'],
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ([2, 3] as $numero) {
                $campos = collect(['nombre', 'localidad', 'reclamo', 'pretension'])
                    ->map(fn ($campo) => $campo . $numero);

                $completados = $campos->filter(fn ($campo) => filled($request->input($campo)));

                if ($completados->isNotEmpty() && $completados->count() < $campos->count()) {
                    $validator->errors()->add(
                        'nombre' . $numero,
                        'Complete nombre, localidad, reclamo y pretension del denunciado ' . $numero . ', o deje todos vacios.'
                    );
                }
            }
        });

        $data = $validator->validate();

        try {
            $codigo = DB::connection('omic')->transaction(function () use ($data, $request) {
                $denunciados = [
                    $this->guardarDenunciado($data, 1, true),
                    $this->guardarDenunciado($data, 2),
                    $this->guardarDenunciado($data, 3),
                ];

                $archivos = [
                    $this->guardarArchivo($request, 'archivo1'),
                    $this->guardarArchivo($request, 'archivo2'),
                    $this->guardarArchivo($request, 'archivo3'),
                ];

                return DB::connection('omic')
                    ->table('denuncias')
                    ->insertGetId([
                        'apellido' => mb_strtoupper($data['apellido']),
                        'nombre' => mb_strtoupper($data['nombre']),
                        'dni' => $data['dni'],
                        'telefono' => $data['telefono'] ?? '',
                        'celular' => $data['celular'],
                        'email' => $data['email'],
                        'nacimiento' => $data['nacimiento'],
                        'direccion' => $data['direccion'],
                        'id_localidad' => $data['localidad'],
                        'codigo_postal' => $data['cp'] ?? '',
                        'id_denunciado1' => $denunciados[0],
                        'id_denunciado2' => $denunciados[1],
                        'id_denunciado3' => $denunciados[2],
                        'archivo1' => $archivos[0],
                        'archivo2' => $archivos[1],
                        'archivo3' => $archivos[2],
                    ]);
            });

            return redirect()
                ->route('omic.index')
                ->with('omic_codigo_creado', str_pad((string) $codigo, 4, '0', STR_PAD_LEFT));
        } catch (\Throwable $exception) {
            Log::error('Error al cargar reclamo OMIC.', [
                'connection' => 'omic',
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('omic.index')
                ->withInput()
                ->with('omic_mensaje', $this->mensajeError($exception));
        }
    }

    private function localidades(): \Illuminate\Support\Collection
    {
        try {
            return DB::connection('omic')
                ->table('localidades')
                ->orderBy('descripcion')
                ->get(['id_localidad', 'descripcion']);
        } catch (\Throwable $exception) {
            Log::warning('No se pudieron consultar localidades OMIC.', [
                'connection' => 'omic',
                'error' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    private function guardarDenunciado(array $data, int $numero, bool $required = false): int
    {
        if (! $required && blank($data['nombre' . $numero] ?? null)) {
            return 0;
        }

        return DB::connection('omic')
            ->table('denunciados')
            ->insertGetId([
                'nombre' => $data['nombre' . $numero] ?? '',
                'telefono' => $data['telefono' . $numero] ?? '',
                'direccion' => $data['direccion' . $numero] ?? '',
                'localidad' => $data['localidad' . $numero] ?? '',
                'reclamo' => $data['reclamo' . $numero] ?? '',
                'pretension' => $data['pretension' . $numero] ?? '',
                'autorizado' => $data['autorizado' . $numero] ?? 'No',
                'observaciones' => $data['observaciones' . $numero] ?? '',
            ]);
    }

    private function guardarArchivo(Request $request, string $campo): string
    {
        if (! $request->hasFile($campo)) {
            return '';
        }

        return basename($request->file($campo)->store('omic', 'public'));
    }

    private function mensajeError(\Throwable $exception): string
    {
        if (app()->isLocal()) {
            return 'Error local de OMIC: ' . $exception->getMessage();
        }

        return 'No se pudo cargar el reclamo de OMIC en este momento. Intente nuevamente mas tarde.';
    }
}
