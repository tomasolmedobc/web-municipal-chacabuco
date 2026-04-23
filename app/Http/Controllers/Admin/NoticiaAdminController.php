<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use App\Models\NoticiaArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NoticiaAdminController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('q');

        $query = Noticia::query();

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
        return view('admin.noticias.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:oculto,publicado'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        $slugBase = Str::slug($datos['titulo']);
        $slug = $slugBase;
        $contador = 1;

        while (Noticia::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        $rutaImagen = '/images/importantes/default-noticia.webp';

        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = $this->procesarImagen($request->file('imagen_destacada'));

            if ($rutaProcesada) {
                $rutaImagen = $rutaProcesada;
            }
        }

        $noticia = Noticia::create([
            'titulo' => $datos['titulo'],
            'contenido' => $datos['contenido'],
            'fecha' => $datos['fecha'],
            'slug' => $slug,
            'imagen_destacada' => $rutaImagen,
            'estado' => $datos['estado'],
            'user_id' => auth()->id(),
            'autor' => auth()->user()->name,
        ]);

        if ($request->hasFile('archivos')) {
            $this->guardarArchivosAdjuntos($request->file('archivos'), $noticia);
        }

        return redirect()->route('admin.dashboard')->with('ok', 'Noticia creada correctamente.');
    }

    public function edit(Noticia $noticia)
    {
        return view('admin.noticias.edit', compact('noticia'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:oculto,publicado'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'archivos.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:10240'],
        ]);

        if ($noticia->titulo !== $datos['titulo']) {
            $slugBase = Str::slug($datos['titulo']);
            $slug = $slugBase;
            $contador = 1;

            while (
                Noticia::where('slug', $slug)
                    ->where('id', '!=', $noticia->id)
                    ->exists()
            ) {
                $slug = $slugBase . '-' . $contador;
                $contador++;
            }

            $noticia->slug = $slug;
        }

        if ($request->hasFile('imagen_destacada')) {
            $rutaProcesada = $this->procesarImagen($request->file('imagen_destacada'));

            if ($rutaProcesada) {
                $noticia->imagen_destacada = $rutaProcesada;
            }
        }

        $noticia->titulo = $datos['titulo'];
        $noticia->contenido = $datos['contenido'];
        $noticia->fecha = $datos['fecha'];
        $noticia->estado = $datos['estado'];
        $noticia->autor = auth()->user()->name;
        $noticia->user_id = auth()->id();

        $noticia->save();

        if ($request->hasFile('archivos')) {
            $this->guardarArchivosAdjuntos($request->file('archivos'), $noticia);
        }

        return redirect()->route('admin.dashboard')->with('ok', 'Noticia actualizada correctamente.');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();

        return redirect()->route('admin.dashboard')->with('ok', 'Noticia eliminada correctamente.');
    }

    public function toggleStatus(Noticia $noticia)
    {
        $noticia->estado = $noticia->estado === 'publicado' ? 'oculto' : 'publicado';
        $noticia->save();

        $mensaje = $noticia->estado === 'publicado'
            ? 'La noticia fue publicada correctamente.'
            : 'La noticia fue ocultada correctamente.';

        $tipo = $noticia->estado === 'publicado' ? 'success' : 'success';

        return redirect()->back()->with([
            'ok' => $mensaje,
            'ok_type' => $tipo,
        ]);
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
                'message' => 'Archivo adjunto eliminado correctamente.'
            ]);
        }

        return redirect()->back()->with('ok', 'Archivo adjunto eliminado correctamente.');
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
        $nombreBase = time() . '_' . Str::random(6) . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));

        $nombreOriginal = $nombreBase . '.' . $extension;
        $rutaOriginal = $directorioOriginal . '/' . $nombreOriginal;
        $rutaWebp = $directorioWebp . '/' . $nombreBase . '.webp';

        $archivo->move($directorioOriginal, $nombreOriginal);

        if ($extension === 'webp') {
            copy($rutaOriginal, $rutaWebp);
            return "/images/noticias/{$anio}/{$mes}/" . $nombreBase . '.webp';
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

            $imagen = $imagenRedimensionada;
        }

        imagewebp($imagen, $rutaWebp, 85);

        return "/images/noticias/{$anio}/{$mes}/" . $nombreBase . '.webp';
    }

    private function guardarArchivosAdjuntos($archivos, $noticia)
    {
        $anio = now()->format('Y');
        $mes = now()->format('m');

        $directorio = public_path("files/noticias/{$anio}/{$mes}");
        File::ensureDirectoryExists($directorio);

        foreach ($archivos as $archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreBase = time() . '_' . Str::random(6) . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
            $nombreFinal = $nombreBase . '.' . $extension;

            $archivo->move($directorio, $nombreFinal);

            $noticia->archivos()->create([
                'nombre_original' => $archivo->getClientOriginalName(),
                'nombre_archivo' => $nombreFinal,
                'ruta' => "/files/noticias/{$anio}/{$mes}/" . $nombreFinal,
                'mime_type' => $archivo->getClientMimeType(),
                'extension' => $extension,
            ]);
        }
    }
}