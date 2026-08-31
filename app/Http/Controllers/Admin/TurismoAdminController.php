<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Localidad;
use App\Models\TurismoItem;
use App\Models\TurismoItemArchivo;
use App\Models\TurismoItemImagen;
use App\Support\ImageUploader;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Admin\TurismoItemRequest;
use Illuminate\Http\Request;

class TurismoAdminController extends Controller
{
    public function index(Request $request)
    {
        $tipoActivo = TurismoItem::normalizarTipo($request->get('tipo'));
        $localidadId = $request->get('localidad_id');
        $busqueda = $request->get('q');

        $items = TurismoItem::query()
            ->tipo($tipoActivo)
            ->when($localidadId, fn ($query) => $query->where('localidad_id', $localidadId))
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('titulo', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%")
                        ->orWhere('categoria', 'like', "%{$busqueda}%");
                });
            })
            ->with('localidad')
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());

        $totales = collect(TurismoItem::TIPOS)
            ->mapWithKeys(fn ($config, $tipo) => [
                $tipo => TurismoItem::tipo($tipo)->count(),
            ]);

        return view('admin.turismo.index', [
            'items' => $items,
            'tipos' => TurismoItem::TIPOS,
            'tipoActivo' => $tipoActivo,
            'config' => TurismoItem::configTipo($tipoActivo),
            'totales' => $totales,
            'localidades' => Localidad::orderBy('orden')->get(),
            'localidadId' => $localidadId,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(Request $request)
    {
        $tipo = TurismoItem::normalizarTipo($request->get('tipo'));

        return view('admin.turismo.form', [
            'item' => new TurismoItem(['tipo' => $tipo, 'estado' => 'visible', 'localidad_id' => $request->get('localidad_id')]),
            'tipoActivo' => $tipo,
            'tipos' => TurismoItem::TIPOS,
            'config' => TurismoItem::configTipo($tipo),
            'localidades' => Localidad::orderBy('orden')->get(),
            'modo' => 'crear',
        ]);
    }

    public function store(TurismoItemRequest $request)
    {
        $data = $request->validated();
        unset($data['imagen'], $data['galeria'], $data['adjuntos']);
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['destacado'] = $request->boolean('destacado');
        $data['mostrar_detalle'] = $request->boolean('mostrar_detalle');

        $item = TurismoItem::create($data);
        $this->guardarImagen($request, $item);
        $this->guardarArchivos($request, $item);

        AuditLog::registrar('crear', 'TurismoItem', $item->id, "Turismo creado: \"{$item->titulo}\" (tipo: {$item->tipo})");

        return redirect()
            ->route('admin.turismo.index', ['tipo' => $item->tipo])
            ->with('ok', ucfirst(TurismoItem::configTipo($item->tipo)['singular']) . ' creado correctamente');
    }

    public function edit(TurismoItem $turismo)
    {
        return view('admin.turismo.form', [
            'item' => $turismo->load('galeria', 'archivos'),
            'tipoActivo' => $turismo->tipo,
            'tipos' => TurismoItem::TIPOS,
            'config' => TurismoItem::configTipo($turismo->tipo),
            'localidades' => Localidad::orderBy('orden')->get(),
            'modo' => 'editar',
        ]);
    }

    public function update(TurismoItemRequest $request, TurismoItem $turismo)
    {
        $data = $request->validated();
        unset($data['imagen'], $data['galeria'], $data['adjuntos']);
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['destacado'] = $request->boolean('destacado');
        $data['mostrar_detalle'] = $request->boolean('mostrar_detalle');

        $turismo->fill($data)->save();
        $this->guardarImagen($request, $turismo);
        $this->guardarGaleria($request, $turismo);
        $this->guardarArchivos($request, $turismo);

        AuditLog::registrar('editar', 'TurismoItem', $turismo->id, "Turismo editado: \"{$turismo->titulo}\" (tipo: {$turismo->tipo})");

        return redirect()
            ->route('admin.turismo.edit', $turismo)
            ->with('ok', ucfirst(TurismoItem::configTipo($turismo->tipo)['singular']) . ' actualizado correctamente');
    }

    public function destroyArchivo(TurismoItem $turismo, TurismoItemArchivo $archivo)
    {
        abort_if($archivo->turismo_item_id !== $turismo->id, 403);

        $rutaFisica = public_path(ltrim($archivo->ruta, '/'));
        if (File::isFile($rutaFisica)) {
            File::delete($rutaFisica);
        }

        $archivo->delete();

        return back()->with('ok', 'Archivo eliminado.');
    }

    public function destroyImagen(TurismoItem $turismo, TurismoItemImagen $imagen)
    {
        abort_if($imagen->turismo_item_id !== $turismo->id, 403);

        ImageUploader::eliminar($imagen->imagen);
        $imagen->delete();

        return back()->with('ok', 'Imagen eliminada.');
    }

    public function setHeader(TurismoItem $turismo, TurismoItemImagen $imagen)
    {
        abort_if($imagen->turismo_item_id !== $turismo->id, 403);

        $turismo->galeria()->update(['es_header' => false]);
        $imagen->update(['es_header' => true]);

        return back()->with('ok', 'Imagen de portada actualizada.');
    }

    public function destroy(TurismoItem $turismo)
    {
        $tipo = $turismo->tipo;

        AuditLog::registrar('eliminar', 'TurismoItem', $turismo->id, "Turismo eliminado: \"{$turismo->titulo}\" (tipo: {$tipo})");

        ImageUploader::eliminar($turismo->imagen);
        $turismo->delete();

        return redirect()
            ->route('admin.turismo.index', ['tipo' => $tipo])
            ->with('ok', 'Registro eliminado correctamente');
    }

    private function guardarImagen(Request $request, TurismoItem $item): void
    {
        if (! $request->hasFile('imagen')) {
            return;
        }

        $ruta = ImageUploader::guardarWebp($request->file('imagen'), 'turismo/items');

        if (! $ruta) {
            return;
        }

        ImageUploader::eliminar($item->imagen);
        $item->update(['imagen' => $ruta]);
    }

    private function guardarGaleria(Request $request, TurismoItem $item): void
    {
        if (! $request->hasFile('galeria')) {
            return;
        }

        foreach ($request->file('galeria') as $archivo) {
            $ruta = ImageUploader::guardarWebp($archivo, 'turismo/galeria');
            if ($ruta) {
                TurismoItemImagen::create([
                    'turismo_item_id' => $item->id,
                    'imagen'          => $ruta,
                    'orden'           => $item->galeria()->count(),
                ]);
            }
        }
    }

    private function guardarArchivos(Request $request, TurismoItem $item): void
    {
        if (! $request->hasFile('adjuntos')) {
            return;
        }

        $carpeta = 'files/turismo';
        $directorio = public_path($carpeta);
        File::ensureDirectoryExists($directorio);

        foreach ($request->file('adjuntos') as $archivo) {
            $extension    = strtolower($archivo->getClientOriginalExtension());
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreBase   = pathinfo($nombreOriginal, PATHINFO_FILENAME);
            $nombreBase   = preg_replace('/[^a-zA-Z0-9_\-]/', '-', $nombreBase);
            $nombreFinal  = uniqid() . '-' . $nombreBase . '.' . $extension;

            $archivo->move($directorio, $nombreFinal);

            TurismoItemArchivo::create([
                'turismo_item_id' => $item->id,
                'nombre_original' => $nombreOriginal,
                'nombre_archivo'  => $nombreFinal,
                'ruta'            => "/{$carpeta}/{$nombreFinal}",
                'mime_type'       => $archivo->getClientMimeType(),
                'extension'       => $extension,
            ]);
        }
    }
}
