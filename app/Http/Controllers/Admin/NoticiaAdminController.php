<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
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
            'estado' => ['required', 'in:borrador,publicado'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $slugBase = Str::slug($datos['titulo']);
        $slug = $slugBase;
        $contador = 1;

        while (Noticia::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        $rutaImagen = null;

        if ($request->hasFile('imagen_destacada')) {
            File::ensureDirectoryExists(public_path('images/noticias'));

            $archivo = $request->file('imagen_destacada');
            $nombre = time() . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $archivo->getClientOriginalExtension();

            $archivo->move(public_path('images/noticias'), $nombre);
            $rutaImagen = '/images/noticias/' . $nombre;
        }

        Noticia::create([
            'titulo' => $datos['titulo'],
            'contenido' => $datos['contenido'],
            'fecha' => $datos['fecha'],
            'slug' => $slug,
            'imagen_destacada' => $rutaImagen,
            'estado' => $datos['estado'],
            'user_id' => auth()->id(),
            'autor' => auth()->user()->name,
        ]);

        return redirect()->route('admin.noticias.index')->with('ok', 'Noticia creada correctamente.');
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
            'estado' => ['required', 'in:borrador,publicado'],
            'imagen_destacada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            File::ensureDirectoryExists(public_path('images/noticias'));

            $archivo = $request->file('imagen_destacada');
            $nombre = time() . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $archivo->getClientOriginalExtension();

            $archivo->move(public_path('images/noticias'), $nombre);
            $noticia->imagen_destacada = '/images/noticias/' . $nombre;
        }

        $noticia->titulo = $datos['titulo'];
        $noticia->contenido = $datos['contenido'];
        $noticia->fecha = $datos['fecha'];
        $noticia->estado = $datos['estado'];
        $noticia->autor = auth()->user()->name;
        $noticia->user_id = auth()->id();

        $noticia->save();

        return redirect()->route('admin.noticias.index')->with('ok', 'Noticia actualizada correctamente.');
    }

    public function destroy(Noticia $noticia)
    {
        $noticia->delete();

        return redirect()->route('admin.noticias.index')->with('ok', 'Noticia eliminada correctamente.');
    }

    public function toggleStatus(Noticia $noticia)
{
    $noticia->estado = $noticia->estado === 'publicado' ? 'oculto' : 'publicado';
    $noticia->save();

    return redirect()->back()->with('ok', 'Estado actualizado correctamente.');
}
}
