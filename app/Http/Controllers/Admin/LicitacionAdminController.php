<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licitacion;
use App\Models\LicitacionArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LicitacionAdminController extends Controller
{
    public function index(Request $request)
    {
        $categoriaActiva = Licitacion::normalizarCategoria($request->get('categoria'));

        $documentos = Licitacion::with('archivos')
            ->categoria($categoriaActiva)
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $totales = collect(Licitacion::CATEGORIAS)
            ->mapWithKeys(fn ($config, $categoria) => [
                $categoria => Licitacion::categoria($categoria)->count(),
            ]);

        return view('admin.gobierno-abierto.index', [
            'documentos' => $documentos,
            'categorias' => Licitacion::CATEGORIAS,
            'categoriaActiva' => $categoriaActiva,
            'config' => Licitacion::configCategoria($categoriaActiva),
            'totales' => $totales,
        ]);
    }

    public function create(Request $request)
    {
        $categoria = Licitacion::normalizarCategoria($request->get('categoria'));

        return view('admin.gobierno-abierto.form', [
            'documento' => new Licitacion(['categoria' => $categoria]),
            'categorias' => Licitacion::CATEGORIAS,
            'categoriaActiva' => $categoria,
            'config' => Licitacion::configCategoria($categoria),
            'modo' => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $documento = new Licitacion();
        $documento->fill($data);
        $documento->save();

        $this->guardarArchivos($request, $documento);

        return redirect()
            ->route('admin.gobierno-abierto.index', ['categoria' => $documento->categoria])
            ->with('ok', $this->mensajeOk($documento->categoria, 'creado'));
    }

    public function edit(Licitacion $licitacion)
    {
        $licitacion->load('archivos');

        return view('admin.gobierno-abierto.form', [
            'documento' => $licitacion,
            'categorias' => Licitacion::CATEGORIAS,
            'categoriaActiva' => $licitacion->categoria,
            'config' => Licitacion::configCategoria($licitacion->categoria),
            'modo' => 'editar',
        ]);
    }

    public function update(Request $request, Licitacion $licitacion)
    {
        $data = $this->validar($request);

        $licitacion->fill($data);
        $licitacion->save();
        $this->guardarArchivos($request, $licitacion);

        return redirect()
            ->route('admin.gobierno-abierto.index', ['categoria' => $licitacion->categoria])
            ->with('ok', $this->mensajeOk($licitacion->categoria, 'actualizado'));
    }

    public function destroy(Licitacion $licitacion)
    {
        $categoria = $licitacion->categoria;

        $licitacion->load('archivos');

        foreach ($licitacion->archivos as $archivo) {
            $this->eliminarArchivoFisico($archivo->ruta);
        }

        $licitacion->delete();

        return redirect()
            ->route('admin.gobierno-abierto.index', ['categoria' => $categoria])
            ->with('ok', $this->mensajeOk($categoria, 'eliminado'));
    }

    public function destroyArchivo(LicitacionArchivo $archivo)
    {
        $this->eliminarArchivoFisico($archivo->ruta);
        $archivo->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Archivo adjunto eliminado correctamente.',
            ]);
        }

        return redirect()
            ->back()
            ->with('ok', 'Archivo adjunto eliminado correctamente.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'categoria' => ['required', Rule::in(array_keys(Licitacion::CATEGORIAS))],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'tipo' => ['required', 'in:publica,privada'],
            'estado' => ['required', 'in:activa,finalizada'],
            'numero_expediente' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer'],
            'fecha_publicacion' => ['nullable', 'date'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
    }

    private function guardarArchivos(Request $request, Licitacion $documento): void
    {
        if (! $request->hasFile('archivos')) {
            return;
        }

        foreach ($request->file('archivos') as $archivo) {
            $ruta = $archivo->store('gobierno-abierto/' . $documento->categoria, 'public');

            $documento->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'nombre_archivo' => basename($ruta),
                'ruta' => '/storage/' . $ruta,
                'mime_type' => $archivo->getMimeType(),
                'extension' => strtolower($archivo->getClientOriginalExtension()),
                'tamano' => $archivo->getSize(),
            ]);

            if (! $documento->archivo_ruta) {
                $documento->archivo_nombre = $archivo->getClientOriginalName();
                $documento->archivo_ruta = '/storage/' . $ruta;
                $documento->archivo_mime = $archivo->getMimeType();
                $documento->archivo_peso = $archivo->getSize();
                $documento->save();
            }
        }
    }

    private function eliminarArchivoFisico(?string $ruta): void
    {
        if (! $ruta) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $ruta));
    }

    private function mensajeOk(string $categoria, string $accion): string
    {
        $config = Licitacion::configCategoria($categoria);

        return ucfirst($config['singular']) . " {$accion} correctamente";
    }
}
