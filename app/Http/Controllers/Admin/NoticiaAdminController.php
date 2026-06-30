<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Noticia;
use App\Models\NoticiaArchivo;
use App\Models\Categoria;
use App\Http\Requests\Admin\NoticiaRequest;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NoticiaAdminController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('q');

        $query = Noticia::with('categorias');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                    ->orWhere('contenido', 'like', '%' . $busqueda . '%')
                    ->orWhere('slug', 'like', '%' . $busqueda . '%')
                    ->orWhere('autor', 'like', '%' . $busqueda . '%');
            });
        }

        $noticias = $query
            ->orderBy('fecha', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.noticias.index', compact('noticias', 'busqueda'));
    }

    public function create()
    {
        $categoriasPadre = Categoria::with('hijas')
            ->whereNull('parent_id')
            ->orderBy('nombre')
            ->get();

        return view('admin.noticias.create', compact('categoriasPadre'));
    }

    public function store(NoticiaRequest $request)
    {
        $datos = $request->validated();

        $slug = $this->generarSlugUnico($datos['titulo']);

        $rutaImagen = null;
        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = ImageUploader::guardarWebpConOriginal($request->file('imagen_destacada'), 'noticias');

            if ($rutaProcesada) {
                $rutaImagen = $rutaProcesada;
            }
        }

        $esDestacada = $request->boolean('destacada');

        if ($esDestacada) {
            $this->limpiarDestacadas();
        }

        $noticia = Noticia::create([
            'titulo' => $datos['titulo'],
            'contenido' => $datos['contenido'],
            'fecha' => $datos['fecha'],
            'slug' => $slug,
            'imagen_destacada' => $rutaImagen,
            'estado' => $datos['estado'],
            'destacada' => $esDestacada,
            'destacada_hasta' => $esDestacada ? now()->addDays((int) $request->input('destacada_dias', 12)) : null,
            'user_id' => Auth::id(),
            'autor' => Auth::user()->name,
        ]);

        $noticia->categorias()->sync($datos['categorias'] ?? []);

        if ($request->hasFile('archivos')) {
            $this->guardarArchivosAdjuntos($request->file('archivos'), $noticia);
        }

        AuditLog::registrar('crear', 'Noticia', $noticia->id, "Noticia creada: \"{$noticia->titulo}\"");

        return redirect()
            ->route('admin.dashboard')
            ->with('ok', 'Noticia creada correctamente.');
    }

    public function edit(Noticia $noticia)
    {
        $categoriasPadre = Categoria::with('hijas')
            ->whereNull('parent_id')
            ->orderBy('nombre')
            ->get();

        $noticia->load('categorias');

        return view('admin.noticias.edit', compact('noticia', 'categoriasPadre'));
    }

    public function update(NoticiaRequest $request, Noticia $noticia)
    {
        $datos = $request->validated();

        if ($noticia->titulo !== $datos['titulo']) {
            $noticia->slug = $this->generarSlugUnico($datos['titulo'], $noticia->id);
        }

        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = ImageUploader::guardarWebpConOriginal($request->file('imagen_destacada'), 'noticias');

            if ($rutaProcesada) {
                $noticia->imagen_destacada = $rutaProcesada;
            }
        }

        $esDestacada = $request->boolean('destacada');

        if ($esDestacada) {
            $this->limpiarDestacadas($noticia->id);
        }

        $noticia->titulo = $datos['titulo'];
        $noticia->contenido = $datos['contenido'];
        $noticia->fecha = $datos['fecha'];
        $noticia->estado = $datos['estado'];
        $noticia->destacada = $esDestacada;
        $noticia->destacada_hasta = $esDestacada ? now()->addDays((int) $request->input('destacada_dias', 12)) : null;
        $noticia->autor = Auth::user()->name;
        $noticia->user_id = Auth::id();

        $noticia->save();

        $noticia->categorias()->sync($datos['categorias'] ?? []);

        if ($request->hasFile('archivos')) {
            $this->guardarArchivosAdjuntos($request->file('archivos'), $noticia);
        }

        AuditLog::registrar('editar', 'Noticia', $noticia->id, "Noticia editada: \"{$noticia->titulo}\"");

        return redirect()
            ->route('admin.dashboard')
            ->with('ok', 'Noticia actualizada correctamente.');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->load('archivos');

        foreach ($noticia->archivos as $archivo) {
            $this->eliminarArchivoPublico($archivo->ruta);
        }

        $this->eliminarImagenNoticia($noticia->imagen_destacada);

        AuditLog::registrar('eliminar', 'Noticia', $noticia->id, "Noticia eliminada: \"{$noticia->titulo}\"");

        $noticia->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('ok', 'Noticia eliminada correctamente.');
    }

    public function toggleStatus(Noticia $noticia)
    {
        $noticia->estado = $noticia->estado === 'publicado' ? 'oculto' : 'publicado';
        $noticia->save();

        $mensaje = $noticia->estado === 'publicado'
            ? 'La noticia fue publicada correctamente.'
            : 'La noticia fue ocultada correctamente.';

        return redirect()->back()->with('ok', $mensaje);
    }

    public function destroyArchivo(NoticiaArchivo $archivo)
    {
        $this->eliminarArchivoPublico($archivo->ruta);

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

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if (! $ruta) {
            return;
        }

        $rutaNormalizada = '/' . ltrim(str_replace('\\', '/', $ruta), '/');

        $prefijosPermitidos = ['/images/noticias/', '/files/noticias/', '/storage/'];

        $permitida = false;
        foreach ($prefijosPermitidos as $prefijo) {
            if (str_starts_with($rutaNormalizada, $prefijo)) {
                $permitida = true;
                break;
            }
        }

        if (! $permitida) {
            return;
        }

        $rutaFisica = public_path(ltrim($rutaNormalizada, '/'));

        if (File::exists($rutaFisica) && File::isFile($rutaFisica)) {
            File::delete($rutaFisica);
        }
    }

    private function eliminarImagenNoticia(?string $rutaImagen): void
    {
        if (! $rutaImagen) {
            return;
        }

        $this->eliminarArchivoPublico($rutaImagen);

        if (! str_starts_with($rutaImagen, '/images/noticias/')) {
            return;
        }

        $partes = explode('/', trim($rutaImagen, '/'));

        if (count($partes) < 5) {
            return;
        }

        [, , $anio, $mes, $archivo] = $partes;
        $nombreBase = pathinfo($archivo, PATHINFO_FILENAME);
        $directorioOriginal = public_path("uploads_originales/noticias/{$anio}/{$mes}");

        foreach (glob($directorioOriginal . '/' . $nombreBase . '.*') ?: [] as $original) {
            if (File::isFile($original)) {
                File::delete($original);
            }
        }
    }

    private function generarSlugUnico(string $titulo, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($titulo);
        $slug = $slugBase;
        $contador = 1;

        while (
            Noticia::where('slug', $slug)
                ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
                ->exists()
        ) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    private function limpiarDestacadas(?int $ignorarId = null): void
    {
        Noticia::where('destacada', true)
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->update([
                'destacada' => false,
                'destacada_hasta' => null,
            ]);
    }

    private function guardarArchivosAdjuntos($archivos, Noticia $noticia): void
    {
        $anio = now()->format('Y');
        $mes = now()->format('m');

        $carpetaPublica = "files/noticias/{$anio}/{$mes}";
        $directorio = public_path($carpetaPublica);
        File::ensureDirectoryExists($directorio);

        foreach ($archivos as $archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreBase = ImageUploader::nombreBaseFecha(pathinfo($nombreOriginal, PATHINFO_FILENAME));
            $nombreFinal = $nombreBase . '.' . $extension;

            if (str_contains(Str::lower($nombreOriginal), 'ordenanza')) {
                $nombreFinal = $this->nombreStorageUnico('Ordenanza', $nombreBase, $extension);
                $rutaStorage = $archivo->storeAs('Ordenanza', $nombreFinal, 'public');
                $rutaPublica = '/storage/' . $rutaStorage;
            } else {
                $nombreBase = ImageUploader::nombreBaseUnico($directorio, $nombreBase, $extension);
                $nombreFinal = $nombreBase . '.' . $extension;
                $archivo->move($directorio, $nombreFinal);
                $rutaPublica = "/{$carpetaPublica}/{$nombreFinal}";
            }

            $noticia->archivos()->create([
                'nombre_original' => $nombreOriginal,
                'nombre_archivo' => $nombreFinal,
                'ruta' => $rutaPublica,
                'mime_type' => $archivo->getClientMimeType(),
                'extension' => $extension,
            ]);
        }
    }

    private function nombreStorageUnico(string $carpeta, string $nombreBase, string $extension): string
    {
        $nombreDisponible = $nombreBase . '.' . $extension;
        $contador = 2;

        while (Storage::disk('public')->exists(trim($carpeta, '/') . '/' . $nombreDisponible)) {
            $nombreDisponible = $nombreBase . '_' . $contador . '.' . $extension;
            $contador++;
        }

        return $nombreDisponible;
    }
}
