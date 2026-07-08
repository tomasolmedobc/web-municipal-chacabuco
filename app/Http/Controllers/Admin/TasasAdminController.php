<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TasasConfiguracion;
use App\Models\TasasGrupo;
use App\Models\TasasCuota;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TasasAdminController extends Controller
{
    public function index()
    {
        return view('admin.tasas.index', [
            'config'      => TasasConfiguracion::instancia(),
            'totalGrupos' => TasasGrupo::count(),
            'totalCuotas' => TasasCuota::count(),
        ]);
    }

    public function editConfig()
    {
        return view('admin.tasas.config', [
            'config' => TasasConfiguracion::instancia(),
        ]);
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'texto_pago_anual'           => ['nullable', 'string'],
            'texto_plan_facilidades'     => ['nullable', 'string'],
            'texto_info_bancaria'        => ['nullable', 'string'],
            'banner_url'                 => ['nullable', 'url', 'max:500'],
            'banner_imagen'              => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'btn_consulta_tipo'          => ['required', 'in:url,archivo'],
            'btn_consulta_url'           => ['nullable', 'url', 'max:500'],
            'btn_consulta_archivo'       => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
            'btn_ordenanza_tipo'         => ['required', 'in:url,archivo'],
            'btn_ordenanza_url'          => ['nullable', 'url', 'max:500'],
            'btn_ordenanza_archivo'      => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $config = TasasConfiguracion::instancia();

        $config->texto_pago_anual       = $request->input('texto_pago_anual');
        $config->texto_plan_facilidades = $request->input('texto_plan_facilidades');
        $config->texto_info_bancaria    = $request->input('texto_info_bancaria');
        $config->banner_url             = $request->input('banner_url');

        if ($request->hasFile('banner_imagen')) {
            $this->eliminarArchivo($config->banner_imagen_ruta);
            $config->banner_imagen_ruta = '/storage/' . $this->guardarArchivo(
                $request->file('banner_imagen'), 'tasas/banner'
            );
        }

        $this->procesarBoton($request, $config, 'consulta');
        $this->procesarBoton($request, $config, 'ordenanza');

        $config->save();

        AuditLog::registrar('editar', 'TasasConfiguracion', $config->id, 'Configuración de Tasas Municipales actualizada.');

        return redirect()->route('admin.tasas.index')->with('ok', 'Configuración guardada correctamente.');
    }

    private function procesarBoton(Request $request, TasasConfiguracion $config, string $btn): void
    {
        $tipo = $request->input("btn_{$btn}_tipo");

        $config->{"btn_{$btn}_tipo"} = $tipo;

        if ($tipo === 'url') {
            $config->{"btn_{$btn}_url"}            = $request->input("btn_{$btn}_url");
            $config->{"btn_{$btn}_archivo_nombre"} = null;
            $config->{"btn_{$btn}_archivo_ruta"}   = null;
        } elseif ($request->hasFile("btn_{$btn}_archivo")) {
            $file = $request->file("btn_{$btn}_archivo");

            $this->eliminarArchivo($config->{"btn_{$btn}_archivo_ruta"});

            $config->{"btn_{$btn}_url"}            = null;
            $config->{"btn_{$btn}_archivo_nombre"} = $file->getClientOriginalName();
            $config->{"btn_{$btn}_archivo_ruta"}   = '/storage/' . $this->guardarArchivo($file, 'tasas/botones');
        }
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
