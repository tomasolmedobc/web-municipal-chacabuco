<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObraAnexoAdminController extends Controller
{
    public function index()
    {
        $anexos = ObraAnexo::orderBy('orden')->orderBy('nombre')->paginate(20);

        return view('admin.obras-particulares.anexos.index', compact('anexos'));
    }

    public function create()
    {
        return view('admin.obras-particulares.anexos.form', [
            'anexo' => new ObraAnexo(),
            'modo'  => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'archivo'     => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $anexo = ObraAnexo::create($data);
        $this->guardarArchivo($request, $anexo);

        AuditLog::registrar('crear', 'ObraAnexo', $anexo->id, "Anexo creado: \"{$anexo->nombre}\"");

        return redirect()
            ->route('admin.obras.anexos.index')
            ->with('ok', 'Anexo creado correctamente.');
    }

    public function edit(ObraAnexo $anexo)
    {
        return view('admin.obras-particulares.anexos.form', [
            'anexo' => $anexo,
            'modo'  => 'editar',
        ]);
    }

    public function update(Request $request, ObraAnexo $anexo)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'archivo'     => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $anexo->fill($data)->save();
        $this->guardarArchivo($request, $anexo);

        AuditLog::registrar('editar', 'ObraAnexo', $anexo->id, "Anexo editado: \"{$anexo->nombre}\"");

        return redirect()
            ->route('admin.obras.anexos.index')
            ->with('ok', 'Anexo actualizado correctamente.');
    }

    public function destroy(ObraAnexo $anexo)
    {
        AuditLog::registrar('eliminar', 'ObraAnexo', $anexo->id, "Anexo eliminado: \"{$anexo->nombre}\"");
        $this->eliminarArchivoFisico($anexo->archivo_ruta);
        $anexo->delete();

        return redirect()
            ->route('admin.obras.anexos.index')
            ->with('ok', 'Anexo eliminado correctamente.');
    }

    private function guardarArchivo(Request $request, ObraAnexo $anexo): void
    {
        if (! $request->hasFile('archivo')) {
            return;
        }

        $this->eliminarArchivoFisico($anexo->archivo_ruta);

        $archivo   = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $carpeta   = 'obras-particulares/anexos';
        $base      = now()->format('dmY_Hi') . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
        $nombre    = $this->nombreUnico($carpeta, $base, $extension);
        $ruta      = $archivo->storeAs($carpeta, $nombre, 'public');

        $anexo->update([
            'archivo_nombre' => $archivo->getClientOriginalName(),
            'archivo_ruta'   => '/storage/' . $ruta,
            'archivo_mime'   => $archivo->getMimeType(),
            'archivo_peso'   => $archivo->getSize(),
        ]);
    }

    private function nombreUnico(string $carpeta, string $base, string $ext): string
    {
        $nombre = $base . '.' . $ext;
        $i = 2;

        while (Storage::disk('public')->exists("{$carpeta}/{$nombre}")) {
            $nombre = "{$base}_{$i}.{$ext}";
            $i++;
        }

        return $nombre;
    }

    private function eliminarArchivoFisico(?string $ruta): void
    {
        if ($ruta && str_starts_with($ruta, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $ruta));
        }
    }
}
