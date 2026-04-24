<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class SistemaController extends Controller
{
    public function index()
    {
        return view('admin.sistema.index', [
            'logo' => config_sistema('logo'),
            'portada' => config_sistema('portada'),
            'default_noticia' => config_sistema('default_noticia'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'default_noticia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->boolean('eliminar_logo')) {
            Configuracion::where('clave', 'logo')->delete();
        }

        if ($request->boolean('eliminar_portada')) {
            Configuracion::where('clave', 'portada')->delete();
        }

        if ($request->boolean('eliminar_default_noticia')) {
            Configuracion::where('clave', 'default_noticia')->delete();
        }

        if ($request->hasFile('logo')) {
            $ruta = $request->file('logo')->store('config', 'public');

            Configuracion::updateOrCreate(
                ['clave' => 'logo'],
                ['valor' => '/storage/' . $ruta]
            );
        }

        if ($request->hasFile('portada')) {
            $ruta = $request->file('portada')->store('config', 'public');

            Configuracion::updateOrCreate(
                ['clave' => 'portada'],
                ['valor' => '/storage/' . $ruta]
            );
        }

        if ($request->hasFile('default_noticia')) {
            $ruta = $request->file('default_noticia')->store('config', 'public');

            Configuracion::updateOrCreate(
                ['clave' => 'default_noticia'],
                ['valor' => '/storage/' . $ruta]
            );
        }

        return back()->with('ok', 'Configuración actualizada correctamente');
    }
}