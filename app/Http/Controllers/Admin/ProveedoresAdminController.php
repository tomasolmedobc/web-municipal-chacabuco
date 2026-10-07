<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ProveedoresConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProveedoresAdminController extends Controller
{
    public function index()
    {
        return view('admin.proveedores.index', [
            'config' => ProveedoresConfiguracion::instancia(),
        ]);
    }

    public function editConfig()
    {
        return view('admin.proveedores.config', [
            'config' => ProveedoresConfiguracion::instancia(),
        ]);
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'guia_video_url'         => ['nullable', 'url', 'max:500'],
            'guia_imagen_1'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'guia_imagen_1_titulo'   => ['nullable', 'string', 'max:200'],
            'guia_imagen_1_desc'     => ['nullable', 'string', 'max:500'],
            'guia_imagen_2'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'guia_imagen_2_titulo'   => ['nullable', 'string', 'max:200'],
            'guia_imagen_2_desc'     => ['nullable', 'string', 'max:500'],
            'texto_requisitos'       => ['nullable', 'string'],
            'texto_info_bancaria'    => ['nullable', 'string'],
            'texto_email_contacto'   => ['nullable', 'string'],
            'texto_creacion_usuario' => ['nullable', 'string'],
        ]);

        $config = ProveedoresConfiguracion::instancia();

        $config->guia_video_url   = trim($request->input('guia_video_url', '')) ?: null;
        $config->guia_imagen_1_titulo = $request->input('guia_imagen_1_titulo');
        $config->guia_imagen_1_desc   = $request->input('guia_imagen_1_desc');
        $config->guia_imagen_2_titulo = $request->input('guia_imagen_2_titulo');
        $config->guia_imagen_2_desc   = $request->input('guia_imagen_2_desc');

        if ($request->boolean('eliminar_imagen_1')) {
            $this->eliminarArchivo($config->guia_imagen_1_ruta);
            $config->guia_imagen_1_ruta = null;
        } elseif ($request->hasFile('guia_imagen_1')) {
            $this->eliminarArchivo($config->guia_imagen_1_ruta);
            $config->guia_imagen_1_ruta = '/storage/' . $this->guardarArchivo($request->file('guia_imagen_1'), 'proveedores/guia');
        }

        if ($request->boolean('eliminar_imagen_2')) {
            $this->eliminarArchivo($config->guia_imagen_2_ruta);
            $config->guia_imagen_2_ruta = null;
        } elseif ($request->hasFile('guia_imagen_2')) {
            $this->eliminarArchivo($config->guia_imagen_2_ruta);
            $config->guia_imagen_2_ruta = '/storage/' . $this->guardarArchivo($request->file('guia_imagen_2'), 'proveedores/guia');
        }

        $config->texto_requisitos       = $request->input('texto_requisitos');
        $config->texto_info_bancaria    = $request->input('texto_info_bancaria');
        $config->texto_email_contacto   = $request->input('texto_email_contacto');
        $config->texto_creacion_usuario = $request->input('texto_creacion_usuario');

        $config->save();

        AuditLog::registrar('editar', 'ProveedoresConfiguracion', $config->id, 'Configuración de Proveedores actualizada.');

        return redirect()->route('admin.proveedores.index')->with('ok', 'Configuración guardada correctamente.');
    }

    private function guardarArchivo(UploadedFile $file, string $carpeta): string
    {
        $ext    = strtolower($file->getClientOriginalExtension());
        $base   = now()->format('dmY_Hi') . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $nombre = $base . '.' . $ext;
        $i      = 2;

        while (Storage::disk('public')->exists("{$carpeta}/{$nombre}")) {
            $nombre = $base . "_{$i}.{$ext}";
            $i++;
        }

        return $file->storeAs($carpeta, $nombre, 'public');
    }

    private function eliminarArchivo(?string $rutaConPrefix): void
    {
        if (! $rutaConPrefix) return;

        $ruta = ltrim(str_replace('/storage/', '', $rutaConPrefix), '/');
        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }
    }
}
