<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraNormativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObraNormativaAdminController extends Controller
{
    public function index(Request $request)
    {
        $seccion  = $request->query('seccion', 'obras');
        $seccion  = array_key_exists($seccion, ObraNormativa::SECCIONES) ? $seccion : 'obras';
        $busqueda = $request->query('q');

        $normativas = ObraNormativa::query()
            ->seccion($seccion)
            ->when($busqueda, fn ($q) => $q->where('nombre', 'like', "%{$busqueda}%"))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(20)
            ->appends($request->query());

        $totales = collect(ObraNormativa::SECCIONES)
            ->mapWithKeys(fn ($_, $sec) => [$sec => ObraNormativa::seccion($sec)->count()]);

        return view('admin.obras-particulares.normativas.index', [
            'normativas'    => $normativas,
            'secciones'     => ObraNormativa::SECCIONES,
            'seccionActiva' => $seccion,
            'totales'       => $totales,
            'busqueda'      => $busqueda,
        ]);
    }

    public function create(Request $request)
    {
        $seccion = $request->query('seccion', 'obras');
        $seccion = array_key_exists($seccion, ObraNormativa::SECCIONES) ? $seccion : 'obras';

        return view('admin.obras-particulares.normativas.form', [
            'normativa'     => new ObraNormativa(['seccion' => $seccion, 'visible' => true]),
            'seccionActiva' => $seccion,
            'secciones'     => ObraNormativa::SECCIONES,
            'modo'          => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'seccion' => ['required', 'in:obras,balcones'],
            'nombre'  => ['required', 'string', 'max:255'],
            'orden'   => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible' => ['nullable', 'boolean'],
            'archivo' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $normativa = ObraNormativa::create($data);
        $this->guardarArchivo($request, $normativa);

        AuditLog::registrar('crear', 'ObraNormativa', $normativa->id, "Normativa creada: \"{$normativa->nombre}\"");

        return redirect()
            ->route('admin.obras.normativas.index', ['seccion' => $normativa->seccion])
            ->with('ok', 'Normativa creada correctamente.');
    }

    public function edit(ObraNormativa $normativa)
    {
        return view('admin.obras-particulares.normativas.form', [
            'normativa'     => $normativa,
            'seccionActiva' => $normativa->seccion,
            'secciones'     => ObraNormativa::SECCIONES,
            'modo'          => 'editar',
        ]);
    }

    public function update(Request $request, ObraNormativa $normativa)
    {
        $data = $request->validate([
            'seccion' => ['required', 'in:obras,balcones'],
            'nombre'  => ['required', 'string', 'max:255'],
            'orden'   => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible' => ['nullable', 'boolean'],
            'archivo' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $normativa->fill($data)->save();
        $this->guardarArchivo($request, $normativa);

        AuditLog::registrar('editar', 'ObraNormativa', $normativa->id, "Normativa editada: \"{$normativa->nombre}\"");

        return redirect()
            ->route('admin.obras.normativas.index', ['seccion' => $normativa->seccion])
            ->with('ok', 'Normativa actualizada correctamente.');
    }

    public function destroy(ObraNormativa $normativa)
    {
        $seccion = $normativa->seccion;
        AuditLog::registrar('eliminar', 'ObraNormativa', $normativa->id, "Normativa eliminada: \"{$normativa->nombre}\"");
        $this->eliminarArchivoFisico($normativa->archivo_ruta);
        $normativa->delete();

        return redirect()
            ->route('admin.obras.normativas.index', ['seccion' => $seccion])
            ->with('ok', 'Normativa eliminada correctamente.');
    }

    private function guardarArchivo(Request $request, ObraNormativa $normativa): void
    {
        if (! $request->hasFile('archivo')) {
            return;
        }

        $this->eliminarArchivoFisico($normativa->archivo_ruta);

        $archivo   = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $carpeta   = 'obras-particulares/normativas/' . $normativa->seccion;
        $base      = now()->format('dmY_Hi') . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
        $nombre    = $this->nombreUnico($carpeta, $base, $extension);
        $ruta      = $archivo->storeAs($carpeta, $nombre, 'public');

        $normativa->update([
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
