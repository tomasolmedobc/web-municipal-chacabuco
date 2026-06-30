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
        $localidades = Localidad::orderBy('orden')->get();

        return view('admin.turismo.localidades.index', [
            'localidades' => $localidades,
        ]);
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
}
