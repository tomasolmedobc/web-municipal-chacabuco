<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Localidad;
use App\Models\TurismoItem;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'item' => new TurismoItem(['tipo' => $tipo, 'estado' => 'visible']),
            'tipoActivo' => $tipo,
            'tipos' => TurismoItem::TIPOS,
            'config' => TurismoItem::configTipo($tipo),
            'localidades' => Localidad::orderBy('orden')->get(),
            'modo' => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['destacado'] = $request->boolean('destacado');

        $item = TurismoItem::create($data);
        $this->guardarImagen($request, $item);

        AuditLog::registrar('crear', 'TurismoItem', $item->id, "Turismo creado: \"{$item->titulo}\" (tipo: {$item->tipo})");

        return redirect()
            ->route('admin.turismo.index', ['tipo' => $item->tipo])
            ->with('ok', ucfirst(TurismoItem::configTipo($item->tipo)['singular']) . ' creado correctamente');
    }

    public function edit(TurismoItem $turismo)
    {
        return view('admin.turismo.form', [
            'item' => $turismo,
            'tipoActivo' => $turismo->tipo,
            'tipos' => TurismoItem::TIPOS,
            'config' => TurismoItem::configTipo($turismo->tipo),
            'localidades' => Localidad::orderBy('orden')->get(),
            'modo' => 'editar',
        ]);
    }

    public function update(Request $request, TurismoItem $turismo)
    {
        $data = $this->validar($request);
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['destacado'] = $request->boolean('destacado');

        $turismo->fill($data)->save();
        $this->guardarImagen($request, $turismo);

        AuditLog::registrar('editar', 'TurismoItem', $turismo->id, "Turismo editado: \"{$turismo->titulo}\" (tipo: {$turismo->tipo})");

        return redirect()
            ->route('admin.turismo.index', ['tipo' => $turismo->tipo])
            ->with('ok', ucfirst(TurismoItem::configTipo($turismo->tipo)['singular']) . ' actualizado correctamente');
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

    private function validar(Request $request): array
    {
        $tipo = TurismoItem::normalizarTipo($request->input('tipo'));
        $esEvento = $tipo === TurismoItem::TIPO_EVENTO;

        return $request->validate([
            'tipo' => ['required', Rule::in(array_keys(TurismoItem::TIPOS))],
            'localidad_id' => ['required', 'exists:localidades,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'link_externo' => ['nullable', 'url', 'max:2048'],
            'fecha_inicio' => [$esEvento ? 'required' : 'nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'estado' => ['required', 'in:visible,oculto'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:999'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
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
}
