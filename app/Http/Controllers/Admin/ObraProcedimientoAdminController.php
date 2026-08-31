<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraAnexo;
use App\Models\ObraCategoria;
use App\Models\ObraProcedimiento;
use Illuminate\Http\Request;

class ObraProcedimientoAdminController extends Controller
{
    public function index(Request $request)
    {
        $categorias = ObraCategoria::orderBy('orden')->orderBy('nombre')->get();
        $categoriaId = (int) $request->query('categoria', 0);
        $categoriaActiva = $categorias->firstWhere('id', $categoriaId) ?? $categorias->first();

        $procedimientos = ObraProcedimiento::query()
            ->when($categoriaActiva, fn($q) => $q->where('categoria_id', $categoriaActiva->id))
            ->orderBy('orden')
            ->get();

        return view('admin.obras-particulares.procedimientos.index', [
            'procedimientos'  => $procedimientos,
            'categorias'      => $categorias,
            'categoriaActiva' => $categoriaActiva,
        ]);
    }

    public function create(Request $request)
    {
        $categorias = ObraCategoria::orderBy('orden')->orderBy('nombre')->get();
        $categoriaId = (int) $request->query('categoria', 0);
        $categoriaActiva = $categorias->firstWhere('id', $categoriaId);

        return view('admin.obras-particulares.procedimientos.form', [
            'procedimiento' => new ObraProcedimiento([
                'categoria_id' => $categoriaActiva?->id,
                'visible'      => true,
                'orden'        => 0,
            ]),
            'categorias' => $categorias,
            'anexos'     => ObraAnexo::orderBy('orden')->orderBy('nombre')->get(),
            'modo'       => 'crear',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:obras_categorias,id'],
            'codigo'       => ['nullable', 'string', 'max:10'],
            'titulo'       => ['required', 'string', 'max:255'],
            'contenido'    => ['required', 'string'],
            'orden'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'      => ['nullable', 'boolean'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? 0);
        $data['codigo']  = trim($data['codigo'] ?? '');

        $procedimiento = ObraProcedimiento::create($data);

        AuditLog::registrar('crear', 'ObraProcedimiento', $procedimiento->id, "Procedimiento creado: \"{$procedimiento->titulo}\"");

        return redirect()
            ->route('admin.obras.procedimientos.index', ['categoria' => $procedimiento->categoria_id])
            ->with('ok', 'Procedimiento creado correctamente.');
    }

    public function edit(ObraProcedimiento $procedimiento)
    {
        return view('admin.obras-particulares.procedimientos.form', [
            'procedimiento' => $procedimiento,
            'categorias'    => ObraCategoria::orderBy('orden')->orderBy('nombre')->get(),
            'anexos'        => ObraAnexo::orderBy('orden')->orderBy('nombre')->get(),
            'modo'          => 'editar',
        ]);
    }

    public function update(Request $request, ObraProcedimiento $procedimiento)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:obras_categorias,id'],
            'codigo'       => ['nullable', 'string', 'max:10'],
            'titulo'       => ['required', 'string', 'max:255'],
            'contenido'    => ['required', 'string'],
            'orden'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'      => ['nullable', 'boolean'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? $procedimiento->orden);
        $data['codigo']  = trim($data['codigo'] ?? '');

        $procedimiento->fill($data)->save();

        AuditLog::registrar('editar', 'ObraProcedimiento', $procedimiento->id, "Procedimiento editado: \"{$procedimiento->titulo}\"");

        return redirect()
            ->route('admin.obras.procedimientos.index', ['categoria' => $procedimiento->categoria_id])
            ->with('ok', 'Procedimiento actualizado correctamente.');
    }

    public function destroy(ObraProcedimiento $procedimiento)
    {
        $categoriaId = $procedimiento->categoria_id;

        AuditLog::registrar('eliminar', 'ObraProcedimiento', $procedimiento->id, "Procedimiento eliminado: \"{$procedimiento->titulo}\"");

        $procedimiento->delete();

        return redirect()
            ->route('admin.obras.procedimientos.index', ['categoria' => $categoriaId])
            ->with('ok', 'Procedimiento eliminado correctamente.');
    }
}
