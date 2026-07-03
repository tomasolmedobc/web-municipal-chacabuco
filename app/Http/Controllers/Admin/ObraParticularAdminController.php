<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraAnexo;
use App\Models\ObraConfiguracion;
use App\Models\ObraNormativa;
use App\Models\ObraProcedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObraParticularAdminController extends Controller
{
    public function index()
    {
        return view('admin.obras-particulares.index', [
            'totalNormativas'     => ObraNormativa::count(),
            'totalAnexos'         => ObraAnexo::count(),
            'totalProcedimientos' => ObraProcedimiento::count(),
            'config'              => ObraConfiguracion::instancia(),
        ]);
    }

    public function editConfig()
    {
        return view('admin.obras-particulares.config', [
            'config' => ObraConfiguracion::instancia(),
        ]);
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'registro_tipo'    => ['required', 'in:url,archivo'],
            'registro_url'     => ['nullable', 'url', 'max:500', 'required_if:registro_tipo,url'],
            'registro_archivo' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $config = ObraConfiguracion::instancia();
        $config->registro_tipo = $request->input('registro_tipo');

        if ($request->input('registro_tipo') === 'url') {
            $config->registro_url             = $request->input('registro_url');
            $config->registro_archivo_nombre  = null;
            $config->registro_archivo_ruta    = null;
            $config->registro_archivo_mime    = null;
            $config->registro_archivo_peso    = null;
        } elseif ($request->hasFile('registro_archivo')) {
            $file = $request->file('registro_archivo');
            $ruta = $file->store('obras-particulares/registro', 'public');

            if ($config->registro_archivo_ruta && Storage::disk('public')->exists($config->registro_archivo_ruta)) {
                Storage::disk('public')->delete($config->registro_archivo_ruta);
            }

            $config->registro_url            = null;
            $config->registro_archivo_nombre = $file->getClientOriginalName();
            $config->registro_archivo_ruta   = $ruta;
            $config->registro_archivo_mime   = $file->getMimeType();
            $config->registro_archivo_peso   = $file->getSize();
        }

        $config->save();

        AuditLog::registrar('editar', 'ObraConfiguracion', $config->id, 'Configuración de Registro de Profesionales actualizada.');

        return redirect()->route('admin.obras.index')->with('ok', 'Configuración guardada correctamente.');
    }
}
