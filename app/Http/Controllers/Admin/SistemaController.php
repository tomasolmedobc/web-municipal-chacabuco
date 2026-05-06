<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $this->eliminarConfiguracionArchivo('logo');
        }

        if ($request->boolean('eliminar_portada')) {
            $this->eliminarConfiguracionArchivo('portada');
        }

        if ($request->boolean('eliminar_default_noticia')) {
            $this->eliminarConfiguracionArchivo('default_noticia');
        }

        if ($request->hasFile('logo')) {
            $this->guardarConfiguracionArchivo($request, 'logo', 'config/logo');
        }

        if ($request->hasFile('portada')) {
            $this->guardarConfiguracionArchivo($request, 'portada', 'config/portada');
        }

        if ($request->hasFile('default_noticia')) {
            $this->guardarConfiguracionArchivo($request, 'default_noticia', 'config/default-noticia');
        }

        return back()->with('ok', 'Configuración actualizada correctamente');
    }

    private function guardarConfiguracionArchivo(Request $request, string $clave, string $carpeta): void
    {
        $this->eliminarArchivoAnterior($clave);

        $ruta = $request->file($clave)->store($carpeta, 'public');

        Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => '/storage/' . $ruta]
        );
    }

    private function eliminarConfiguracionArchivo(string $clave): void
    {
        $this->eliminarArchivoAnterior($clave);

        Configuracion::where('clave', $clave)->delete();
    }

    private function eliminarArchivoAnterior(string $clave): void
    {
        $valorActual = Configuracion::where('clave', $clave)->value('valor');

        if (!$valorActual) {
            return;
        }

        $rutaRelativa = str_replace('/storage/', '', $valorActual);

        if (Storage::disk('public')->exists($rutaRelativa)) {
            Storage::disk('public')->delete($rutaRelativa);
        }
    }
}