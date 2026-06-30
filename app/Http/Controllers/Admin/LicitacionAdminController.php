<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Licitacion;
use App\Models\LicitacionArchivo;
use App\Http\Requests\Admin\LicitacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LicitacionAdminController extends Controller
{
    public function index(Request $request)
    {
        $categoriaActiva = Licitacion::normalizarCategoria($request->get('categoria'));

        $documentos = Licitacion::with('archivos')
            ->categoria($categoriaActiva)
            ->when(
                $categoriaActiva === Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
                fn ($query) => $query->orderBy('orden')->orderBy('titulo'),
                fn ($query) => $query->latest()
            )
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

    public function store(LicitacionRequest $request)
    {
        $data = $request->validated();
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['link_externo'] = $data['link_externo'] ?? null;

        $documento = new Licitacion();
        $documento->fill($data);
        $documento->save();

        $this->guardarArchivos($request, $documento);

        AuditLog::registrar('crear', 'Licitacion', $documento->id, "Licitacion creada: \"{$documento->titulo}\" (categoría: {$documento->categoria})");

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

    public function update(LicitacionRequest $request, Licitacion $licitacion)
    {
        $data = $request->validated();
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['link_externo'] = $data['link_externo'] ?? null;

        $licitacion->fill($data);
        $licitacion->save();
        $this->guardarArchivos($request, $licitacion);

        AuditLog::registrar('editar', 'Licitacion', $licitacion->id, "Licitacion editada: \"{$licitacion->titulo}\" (categoría: {$licitacion->categoria})");

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

        AuditLog::registrar('eliminar', 'Licitacion', $licitacion->id, "Licitacion eliminada: \"{$licitacion->titulo}\" (categoría: {$categoria})");

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

    private function guardarArchivos(Request $request, Licitacion $documento): void
    {
        if (! $request->hasFile('archivos')) {
            return;
        }

        foreach ($request->file('archivos') as $archivo) {
            $carpeta = $this->carpetaArchivo($archivo->getClientOriginalName(), $documento);
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreArchivo = $this->nombreArchivoUnico(
                $carpeta,
                $this->nombreBaseFecha(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)),
                $extension
            );
            $ruta = $archivo->storeAs($carpeta, $nombreArchivo, 'public');

            $documento->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'nombre_archivo' => basename($ruta),
                'ruta' => '/storage/' . $ruta,
                'mime_type' => $archivo->getMimeType(),
                'extension' => $extension,
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

    private function carpetaArchivo(string $nombreOriginal, Licitacion $documento): string
    {
        $esOrdenanza = str_contains(Str::lower($nombreOriginal), 'ordenanza');
        $esOrdenanzaVigente = Str::lower(trim($documento->titulo)) === 'ordenanza vigente';

        if ($esOrdenanza && ! $esOrdenanzaVigente) {
            return 'Ordenanza';
        }

        return 'gobierno-abierto/' . $documento->categoria;
    }

    private function nombreBaseFecha(string $nombreOriginal): string
    {
        return now()->format('dmY_Hi') . '_' . Str::slug($nombreOriginal);
    }

    private function nombreArchivoUnico(string $carpeta, string $nombreBase, string $extension): string
    {
        $nombreDisponible = $nombreBase . '.' . $extension;
        $contador = 2;

        while (Storage::disk('public')->exists(trim($carpeta, '/') . '/' . $nombreDisponible)) {
            $nombreDisponible = $nombreBase . '_' . $contador . '.' . $extension;
            $contador++;
        }

        return $nombreDisponible;
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
