<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Configuracion;
use App\Models\HabilitacionDocumento;
use App\Http\Requests\Admin\HabilitacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HabilitacionAdminController extends Controller
{
    public function index(Request $request)
    {
        $seccionActiva = HabilitacionDocumento::normalizarSeccion($request->get('seccion'));
        $busqueda = $request->get('q');

        $documentos = HabilitacionDocumento::query()
            ->seccion($seccionActiva)
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('titulo', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%")
                        ->orWhere('categoria', 'like', "%{$busqueda}%")
                        ->orWhere('numero', 'like', "%{$busqueda}%")
                        ->orWhere('anio', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());

        $totales = collect(HabilitacionDocumento::SECCIONES)
            ->mapWithKeys(fn ($config, $seccion) => [
                $seccion => HabilitacionDocumento::seccion($seccion)->count(),
            ]);

        return view('admin.habilitaciones.index', [
            'documentos' => $documentos,
            'secciones' => HabilitacionDocumento::SECCIONES,
            'seccionActiva' => $seccionActiva,
            'config' => HabilitacionDocumento::configSeccion($seccionActiva),
            'totales' => $totales,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(Request $request)
    {
        $seccion = HabilitacionDocumento::normalizarSeccion($request->get('seccion'));

        return view('admin.habilitaciones.form', [
            'documento' => new HabilitacionDocumento(['seccion' => $seccion, 'estado' => 'visible']),
            'seccionActiva' => $seccion,
            'config' => HabilitacionDocumento::configSeccion($seccion),
            'modo' => 'crear',
        ]);
    }

    public function store(HabilitacionRequest $request)
    {
        $data = $request->validated();
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['link_externo'] = $data['link_externo'] ?? null;
        $data['descripcion'] = $this->descripcionPorDefecto($data);

        $documento = HabilitacionDocumento::create($data);
        $this->guardarArchivo($request, $documento);

        AuditLog::registrar('crear', 'HabilitacionDocumento', $documento->id, "Habilitación creada: \"{$documento->titulo}\" (sección: {$documento->seccion})");

        return redirect()
            ->route('admin.habilitaciones.index', ['seccion' => $documento->seccion])
            ->with('ok', ucfirst(HabilitacionDocumento::configSeccion($documento->seccion)['singular']) . ' creado correctamente');
    }

    public function edit(HabilitacionDocumento $habilitacion)
    {
        return view('admin.habilitaciones.form', [
            'documento' => $habilitacion,
            'seccionActiva' => $habilitacion->seccion,
            'config' => HabilitacionDocumento::configSeccion($habilitacion->seccion),
            'modo' => 'editar',
        ]);
    }

    public function update(HabilitacionRequest $request, HabilitacionDocumento $habilitacion)
    {
        $data = $request->validated();
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['link_externo'] = $data['link_externo'] ?? null;
        $data['descripcion'] = $this->descripcionPorDefecto($data);

        $habilitacion->fill($data)->save();
        $this->guardarArchivo($request, $habilitacion);

        AuditLog::registrar('editar', 'HabilitacionDocumento', $habilitacion->id, "Habilitación editada: \"{$habilitacion->titulo}\" (sección: {$habilitacion->seccion})");

        return redirect()
            ->route('admin.habilitaciones.index', ['seccion' => $habilitacion->seccion])
            ->with('ok', ucfirst(HabilitacionDocumento::configSeccion($habilitacion->seccion)['singular']) . ' actualizado correctamente');
    }

    public function config()
    {
        return view('admin.habilitaciones.config', [
            'url_prefactibilidad' => config_sistema('hab_tramite_prefactibilidad_url', ''),
            'url_habilitacion'    => config_sistema('hab_tramite_habilitacion_url', ''),
        ]);
    }

    public function configUpdate(Request $request)
    {
        $request->validate([
            'url_prefactibilidad' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/)/'],
            'url_habilitacion'    => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/)/'],
        ], [
            'url_prefactibilidad.regex' => 'Debe ser una URL completa (https://...) o una ruta interna (/pagina).',
            'url_habilitacion.regex'    => 'Debe ser una URL completa (https://...) o una ruta interna (/pagina).',
        ]);

        $this->guardarClave('hab_tramite_prefactibilidad_url', trim($request->input('url_prefactibilidad', '')));
        $this->guardarClave('hab_tramite_habilitacion_url',    trim($request->input('url_habilitacion', '')));

        AuditLog::registrar('editar', 'Habilitaciones', null, 'URLs de trámites online de habilitaciones actualizadas');

        return back()->with('ok', 'URLs actualizadas correctamente');
    }

    private function guardarClave(string $clave, string $valor): void
    {
        if ($valor !== '') {
            Configuracion::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        } else {
            Configuracion::where('clave', $clave)->delete();
        }
        config_sistema_flush($clave);
    }

    public function destroy(HabilitacionDocumento $habilitacion)
    {
        $seccion = $habilitacion->seccion;
        AuditLog::registrar('eliminar', 'HabilitacionDocumento', $habilitacion->id, "Habilitación eliminada: \"{$habilitacion->titulo}\" (sección: {$seccion})");

        $this->eliminarArchivoFisico($habilitacion->archivo_ruta);
        $habilitacion->delete();

        return redirect()
            ->route('admin.habilitaciones.index', ['seccion' => $seccion])
            ->with('ok', 'Registro eliminado correctamente');
    }

    private function guardarArchivo(Request $request, HabilitacionDocumento $documento): void
    {
        if (! $request->hasFile('archivo')) {
            return;
        }

        $this->eliminarArchivoFisico($documento->archivo_ruta);

        $archivo = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $carpeta = $this->carpetaArchivo($archivo->getClientOriginalName(), $documento);
        $nombreArchivo = $this->nombreArchivoUnico(
            $carpeta,
            now()->format('dmY_Hi') . '_' . Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)),
            $extension
        );

        $ruta = $archivo->storeAs($carpeta, $nombreArchivo, 'public');

        $documento->update([
            'archivo_nombre' => $archivo->getClientOriginalName(),
            'archivo_ruta' => '/storage/' . $ruta,
            'archivo_mime' => $archivo->getMimeType(),
            'archivo_peso' => $archivo->getSize(),
        ]);
    }

    private function descripcionPorDefecto(array $data): string
    {
        if (filled($data['descripcion'] ?? null)) {
            return $data['descripcion'];
        }

        if (($data['seccion'] ?? null) === HabilitacionDocumento::SECCION_ORDENANZAS) {
            return 'Normativa vigente vinculada a habilitaciones comerciales.';
        }

        return 'Documento disponible para consultar, descargar o utilizar en tramites de habilitaciones.';
    }

    private function carpetaArchivo(string $nombreOriginal, HabilitacionDocumento $documento): string
    {
        if (
            $documento->seccion === HabilitacionDocumento::SECCION_ORDENANZAS
            || str_contains(Str::lower($nombreOriginal), 'ordenanza')
        ) {
            return 'Ordenanza';
        }

        return 'habilitaciones/' . $documento->seccion;
    }

    private function nombreArchivoUnico(string $carpeta, string $nombreBase, string $extension): string
    {
        $nombreDisponible = $nombreBase . '.' . $extension;
        $contador = 2;

        while (Storage::disk('public')->exists(trim($carpeta, '/') . '/' . $nombreDisponible)) {
            $nombreDisponible = $nombreBase . '_' . $contador . '.' . $extension;
            $contador++;
        }

        return $nombreDisponible;
    }

    private function eliminarArchivoFisico(?string $ruta): void
    {
        if (! $ruta || ! str_starts_with($ruta, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $ruta));
    }
}
