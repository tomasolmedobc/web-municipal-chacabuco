<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TasasGrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TasasGrupoAdminController extends Controller
{
    public function index()
    {
        return view('admin.tasas.grupos.index', [
            'grupos' => TasasGrupo::orderBy('orden')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.tasas.grupos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'orden'  => ['required', 'integer', 'min:0', 'max:99'],
            'estado' => ['required', 'in:visible,oculto'],
        ]);

        $data['codigo'] = Str::slug($data['nombre'], '_');

        $grupo = TasasGrupo::create($data);

        AuditLog::registrar('crear', 'TasasGrupo', $grupo->id, "Grupo de tasas creado: {$grupo->nombre}");

        return redirect()->route('admin.tasas.grupos.index')->with('ok', 'Grupo creado correctamente.');
    }

    public function show(TasasGrupo $grupo)
    {
        $grupo->load(['cuotas' => fn($q) => $q->orderBy('fecha_vencimiento')]);

        return view('admin.tasas.grupos.show', compact('grupo'));
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

        $grupo->update([
            ...$request->only('nombre', 'orden', 'estado'),
            'codigo' => Str::slug($request->input('nombre'), '_'),
        ]);

        AuditLog::registrar('editar', 'TasasGrupo', $grupo->id, "Grupo de tasas editado: {$grupo->nombre}");

        return redirect()->route('admin.tasas.grupos.index')->with('ok', 'Grupo actualizado correctamente.');
    }
}
