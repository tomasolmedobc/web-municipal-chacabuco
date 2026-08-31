<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraCategoria;
use App\Models\ObraNormativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ObraNormativaAdminController extends Controller
{
    public function index(Request $request)
    {
        $categorias = ObraCategoria::orderBy('orden')->orderBy('nombre')->get();
        $categoriaId = (int) $request->query('categoria', 0);
        $categoriaActiva = $categorias->firstWhere('id', $categoriaId) ?? $categorias->first();
        $busqueda = $request->query('q');

        $normativas = ObraNormativa::query()
            ->when($categoriaActiva, fn($q) => $q->where('categoria_id', $categoriaActiva->id))
            ->when($busqueda, fn($q) => $q->where('nombre', 'like', "%{$busqueda}%"))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(20)
            ->appends($request->query());

        $totales = $categorias->mapWithKeys(fn($c) => [
            $c->id => ObraNormativa::where('categoria_id', $c->id)->count(),
        ]);

        return view('admin.obras-particulares.normativas.index', [
            'normativas'      => $normativas,
            'categorias'      => $categorias,
            'categoriaActiva' => $categoriaActiva,
            'totales'         => $totales,
            'busqueda'        => $busqueda,
        ]);
    }

    public function create(Request $request)
    {
        $categorias = ObraCategoria::orderBy('orden')->orderBy('nombre')->get();
        $categoriaId = (int) $request->query('categoria', 0);
        $categoriaActiva = $categorias->firstWhere('id', $categoriaId) ?? $categorias->first();

        return view('admin.obras-particulares.normativas.form', [
            'normativa'       => new ObraNormativa(['categoria_id' => $categoriaActiva?->id, 'visible' => true]),
            'categorias'      => $categorias,
            'categoriaActiva' => $categoriaActiva,
            'modo'            => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:obras_categorias,id'],
            'nombre'       => ['required', 'string', 'max:255'],
            'orden'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'      => ['nullable', 'boolean'],
            'archivo'      => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $normativa = ObraNormativa::create($data);
        $this->guardarArchivo($request, $normativa);

        AuditLog::registrar('crear', 'ObraNormativa', $normativa->id, "Normativa creada: \"{$normativa->nombre}\"");

        return redirect()
            ->route('admin.obras.normativas.index', ['categoria' => $normativa->categoria_id])
            ->with('ok', 'Normativa creada correctamente.');
    }

    public function edit(ObraNormativa $normativa)
    {
        return view('admin.obras-particulares.normativas.form', [
            'normativa'       => $normativa,
            'categorias'      => ObraCategoria::orderBy('orden')->orderBy('nombre')->get(),
            'categoriaActiva' => $normativa->categoria,
            'modo'            => 'editar',
        ]);
    }

    public function update(Request $request, ObraNormativa $normativa)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:obras_categorias,id'],
            'nombre'       => ['required', 'string', 'max:255'],
            'orden'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'      => ['nullable', 'boolean'],
            'archivo'      => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $normativa->fill($data)->save();
        $this->guardarArchivo($request, $normativa);

        AuditLog::registrar('editar', 'ObraNormativa', $normativa->id, "Normativa editada: \"{$normativa->nombre}\"");

        return redirect()
            ->route('admin.obras.normativas.index', ['categoria' => $normativa->categoria_id])
            ->with('ok', 'Normativa actualizada correctamente.');
    }

    public function destroy(ObraNormativa $normativa)
    {
        $categoriaId = $normativa->categoria_id;

        AuditLog::registrar('eliminar', 'ObraNormativa', $normativa->id, "Normativa eliminada: \"{$normativa->nombre}\"");

        $this->eliminarArchivoFisico($normativa->archivo_ruta);
        $normativa->delete();

        return redirect()
            ->route('admin.obras.normativas.index', ['categoria' => $categoriaId])
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
        $carpeta   = 'obras-particulares/normativas/' . $normativa->categoria_id;
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
