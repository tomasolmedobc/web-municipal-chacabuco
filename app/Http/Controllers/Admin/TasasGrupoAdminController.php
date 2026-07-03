<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TasasGrupo;
use Illuminate\Http\Request;

class TasasGrupoAdminController extends Controller
{
    public function index()
    {
        return view('admin.tasas.grupos.index', [
            'grupos' => TasasGrupo::orderBy('orden')->get(),
        ]);
    }

    public function edit(TasasGrupo $grupo)
    {
        return view('admin.tasas.grupos.edit', compact('grupo'));
    }

    public function update(Request $request, TasasGrupo $grupo)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'orden'  => ['required', 'integer', 'min:0', 'max:99'],
            'estado' => ['required', 'in:visible,oculto'],
        ]);

        $grupo->update($request->only('nombre', 'orden', 'estado'));

        AuditLog::registrar('editar', 'TasasGrupo', $grupo->id, "Grupo de tasas editado: {$grupo->nombre}");

        return redirect()->route('admin.tasas.grupos.index')->with('ok', 'Grupo actualizado correctamente.');
    }
}
