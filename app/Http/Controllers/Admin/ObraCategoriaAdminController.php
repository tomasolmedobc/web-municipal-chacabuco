<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraCategoria;
use Illuminate\Http\Request;

class ObraCategoriaAdminController extends Controller
{
    public function index()
    {
        $categorias = ObraCategoria::withCount(['procedimientos', 'normativas'])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('admin.obras-particulares.categorias.index', [
            'categorias' => $categorias,
        ]);
    }

    public function create()
    {
        return view('admin.obras-particulares.categorias.form', [
            'categoria' => new ObraCategoria(['orden' => 0, 'visible' => true]),
            'modo'      => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'     => ['nullable', 'boolean'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $categoria = ObraCategoria::create($data);

        AuditLog::registrar('crear', 'ObraCategoria', $categoria->id, "Categoría creada: \"{$categoria->nombre}\"");

        return redirect()
            ->route('admin.obras.categorias.index')
            ->with('ok', 'Categoría creada correctamente.');
    }

    public function edit(ObraCategoria $categoria)
    {
        return view('admin.obras-particulares.categorias.form', [
            'categoria' => $categoria,
            'modo'      => 'editar',
        ]);
    }

    public function update(Request $request, ObraCategoria $categoria)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'     => ['nullable', 'boolean'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);

        $categoria->fill($data)->save();

        AuditLog::registrar('editar', 'ObraCategoria', $categoria->id, "Categoría editada: \"{$categoria->nombre}\"");

        return redirect()
            ->route('admin.obras.categorias.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    public function destroy(ObraCategoria $categoria)
    {
        AuditLog::registrar('eliminar', 'ObraCategoria', $categoria->id, "Categoría eliminada: \"{$categoria->nombre}\"");

        $categoria->delete();

        return redirect()
            ->route('admin.obras.categorias.index')
            ->with('ok', 'Categoría eliminada. Sus procedimientos y normativas también fueron eliminados.');
    }
}
