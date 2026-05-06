<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Models\NoticiaArchivo;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['exists:categorias,id'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:oculto,publicado'],
            'destacada' => ['nullable', 'boolean'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
            'destacada_dias' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $slug = $this->generarSlugUnico($datos['titulo']);

        $rutaImagen = config_sistema(
            'default_noticia',
            '/images/importantes/default-noticia.webp'
        );

        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = $this->procesarImagen($request->file('imagen_destacada'));

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

    public function update(Request $request, Noticia $noticia)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'categorias' => ['nullable', 'array'],
            'categorias.*' => ['exists:categorias,id'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:oculto,publicado'],
            'destacada' => ['nullable', 'boolean'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
            'destacada_dias' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if ($noticia->titulo !== $datos['titulo']) {
            $noticia->slug = $this->generarSlugUnico($datos['titulo'], $noticia->id);
        }

        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = $this->procesarImagen($request->file('imagen_destacada'));

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

        return redirect()
            ->route('admin.dashboard')
            ->with('ok', 'Noticia actualizada correctamente.');
    }

    public function destroy(Noticia $noticia)
    {
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
        $rutaFisica = public_path(ltrim($archivo->ruta, '/'));

        if (file_exists($rutaFisica)) {
            unlink($rutaFisica);
        }

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

    private function procesarImagen($archivo)
    {
        $anio = now()->format('Y');
        $mes = now()->format('m');

        $directorioOriginal = public_path("uploads_originales/noticias/{$anio}/{$mes}");
        $directorioWebp = public_path("images/noticias/{$anio}/{$mes}");

        File::ensureDirectoryExists($directorioOriginal);
        File::ensureDirectoryExists($directorioWebp);

        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombreBase = time() . '_' . Str::random(6) . '_' . Str::slug(
            pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)
        );

        $nombreOriginal = $nombreBase . '.' . $extension;
        $rutaOriginal = $directorioOriginal . '/' . $nombreOriginal;
        $rutaWebp = $directorioWebp . '/' . $nombreBase . '.webp';

        $archivo->move($directorioOriginal, $nombreOriginal);

        if ($extension === 'webp') {
            copy($rutaOriginal, $rutaWebp);
            return "/images/noticias/{$anio}/{$mes}/{$nombreBase}.webp";
        }

        if (in_array($extension, ['jpg', 'jpeg'])) {
            $imagen = imagecreatefromjpeg($rutaOriginal);
        } elseif ($extension === 'png') {
            $imagen = imagecreatefrompng($rutaOriginal);

            imagepalettetotruecolor($imagen);
            imagealphablending($imagen, true);
            imagesavealpha($imagen, true);
        } else {
            return null;
        }

        if (!$imagen) {
            return null;
        }

        $maxAncho = 1600;
        $anchoOriginal = imagesx($imagen);
        $altoOriginal = imagesy($imagen);

        if ($anchoOriginal > $maxAncho) {
            $nuevoAncho = $maxAncho;
            $nuevoAlto = (int) round(($altoOriginal / $anchoOriginal) * $nuevoAncho);

            $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

            imagealphablending($imagenRedimensionada, false);
            imagesavealpha($imagenRedimensionada, true);

            $transparente = imagecolorallocatealpha($imagenRedimensionada, 0, 0, 0, 127);
            imagefill($imagenRedimensionada, 0, 0, $transparente);

            imagecopyresampled(
                $imagenRedimensionada,
                $imagen,
                0,
                0,
                0,
                0,
                $nuevoAncho,
                $nuevoAlto,
                $anchoOriginal,
                $altoOriginal
            );

            imagedestroy($imagen);
            $imagen = $imagenRedimensionada;
        }

        imagewebp($imagen, $rutaWebp, 85);
        imagedestroy($imagen);

        return "/images/noticias/{$anio}/{$mes}/{$nombreBase}.webp";
    }

    private function guardarArchivosAdjuntos($archivos, Noticia $noticia): void
    {
        $anio = now()->format('Y');
        $mes = now()->format('m');

        $directorio = public_path("files/noticias/{$anio}/{$mes}");
        File::ensureDirectoryExists($directorio);

        foreach ($archivos as $archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreBase = time() . '_' . Str::random(6) . '_' . Str::slug(
                pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)
            );
            $nombreFinal = $nombreBase . '.' . $extension;

            $archivo->move($directorio, $nombreFinal);

            $noticia->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'nombre_archivo' => $nombreFinal,
                'ruta' => "/files/noticias/{$anio}/{$mes}/{$nombreFinal}",
                'mime_type' => $archivo->getClientMimeType(),
                'extension' => $extension,
            ]);
        }
    }
}