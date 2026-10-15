<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Localidad;
use App\Support\ImageUploader;
use App\Http\Requests\Admin\LocalidadRequest;
use Illuminate\Http\Request;

class LocalidadAdminController extends Controller
{
    public function index()
    {
        $localidades = Localidad::withCount('turismoItems')->orderBy('orden')->get();

        return view('admin.turismo.localidades.index', [
            'localidades' => $localidades,
        ]);
    }

    public function create()
    {
        return view('admin.turismo.localidades.create');
    }

    public function store(LocalidadRequest $request)
    {
        $data = $request->validated();
        $data['nombre'] = $request->input('nombre');
        $data['slug']   = \Illuminate\Support\Str::slug($data['nombre']);
        $data['orden']  = (int) ($data['orden'] ?? 0);

        if ($request->hasFile('imagen_portada')) {
            $ruta = ImageUploader::guardarWebp($request->file('imagen_portada'), 'turismo/localidades');
            if ($ruta) {
                $data['imagen_portada'] = $ruta;
            }
        }

        $localidad = Localidad::create($data);

        AuditLog::registrar('crear', 'Localidad', $localidad->id, "Localidad creada: \"{$localidad->nombre}\"");

        return redirect()
            ->route('admin.turismo.localidades.index')
            ->with('ok', 'Localidad creada correctamente');
    }

    public function edit(Localidad $localidad)
    {
        return view('admin.turismo.localidades.form', [
            'localidad' => $localidad,
        ]);
    }

    public function update(LocalidadRequest $request, Localidad $localidad)
    {
        $data = $request->validated();

        $data['orden'] = (int) ($data['orden'] ?? 0);

        if ($request->hasFile('imagen_portada')) {
            $ruta = ImageUploader::guardarWebp($request->file('imagen_portada'), 'turismo/localidades');

            if ($ruta) {
                ImageUploader::eliminar($localidad->imagen_portada);
                $data['imagen_portada'] = $ruta;
            }
        }

        $localidad->fill($data)->save();

        AuditLog::registrar('editar', 'Localidad', $localidad->id, "Localidad editada: \"{$localidad->nombre}\"");

        return redirect()
            ->route('admin.turismo.localidades.index')
            ->with('ok', 'Localidad actualizada correctamente');
    }

    public function destroy(Localidad $localidad)
    {
        // La ciudad cabecera es la base del sitio de turismo: no se elimina.
        if ($localidad->es_cabecera) {
            return back()->with('error', 'La ciudad cabecera no se puede eliminar.');
        }

        // La relación con los atractivos y eventos es en cascada: borrar la localidad borraría todo su
        // contenido (y dejaría sus imágenes y adjuntos sin limpiar). Se pide vaciarla o reubicarlo antes.
        $cantidad = $localidad->turismoItems()->count();

        if ($cantidad > 0) {
            return back()->with('error', "No se puede eliminar \"{$localidad->nombre}\": tiene {$cantidad} atractivo(s) o evento(s). "
                . 'Eliminalos o pasalos a otra localidad primero.');
        }

        $nombre = $localidad->nombre;
        $id = $localidad->id;

        ImageUploader::eliminar($localidad->imagen_portada);
        $localidad->delete();

        AuditLog::registrar('eliminar', 'Localidad', $id, "Localidad eliminada: \"{$nombre}\"");

        return redirect()
            ->route('admin.turismo.localidades.index')
            ->with('ok', 'Localidad eliminada correctamente');
    }
}
