<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LicitacionAdminController extends Controller
{
    public function index()
    {
        $licitaciones = Licitacion::latest()
            ->paginate(15);

        return view('admin.licitaciones.index', compact('licitaciones'));
    }

    public function create()
    {
        return view('admin.licitaciones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],

            'tipo' => ['required', 'in:publica,privada'],
            'estado' => ['required', 'in:activa,finalizada'],

            'numero_expediente' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer'],

            'fecha_publicacion' => ['nullable', 'date'],

            'archivo_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $licitacion = new Licitacion();

        $licitacion->fill($data);

        if ($request->hasFile('archivo_pdf')) {

            $archivo = $request->file('archivo_pdf');

            $ruta = $archivo->store('licitaciones', 'public');

            $licitacion->archivo_nombre = $archivo->getClientOriginalName();
            $licitacion->archivo_ruta = '/storage/' . $ruta;
            $licitacion->archivo_mime = $archivo->getMimeType();
            $licitacion->archivo_peso = $archivo->getSize();
        }

        $licitacion->save();

        return redirect()
            ->route('admin.licitaciones.index')
            ->with('ok', 'Licitación creada correctamente');
    }

    public function edit(Licitacion $licitacion)
    {
        return view('admin.licitaciones.edit', compact('licitacion'));
    }

    public function update(Request $request, Licitacion $licitacion)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],

            'tipo' => ['required', 'in:publica,privada'],
            'estado' => ['required', 'in:activa,finalizada'],

            'numero_expediente' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer'],

            'fecha_publicacion' => ['nullable', 'date'],

            'archivo_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $licitacion->fill($data);

        if ($request->hasFile('archivo_pdf')) {

            if ($licitacion->archivo_ruta) {

                $rutaVieja = str_replace('/storage/', '', $licitacion->archivo_ruta);

                Storage::disk('public')->delete($rutaVieja);
            }

            $archivo = $request->file('archivo_pdf');

            $ruta = $archivo->store('licitaciones', 'public');

            $licitacion->archivo_nombre = $archivo->getClientOriginalName();
            $licitacion->archivo_ruta = '/storage/' . $ruta;
            $licitacion->archivo_mime = $archivo->getMimeType();
            $licitacion->archivo_peso = $archivo->getSize();
        }

        $licitacion->save();

        return redirect()
            ->route('admin.licitaciones.index')
            ->with('ok', 'Licitación actualizada correctamente');
    }

    public function destroy(Licitacion $licitacion)
    {
        if ($licitacion->archivo_ruta) {

            $ruta = str_replace('/storage/', '', $licitacion->archivo_ruta);

            Storage::disk('public')->delete($ruta);
        }

        $licitacion->delete();

        return redirect()
            ->route('admin.licitaciones.index')
            ->with('ok', 'Licitación eliminada correctamente');
    }
}