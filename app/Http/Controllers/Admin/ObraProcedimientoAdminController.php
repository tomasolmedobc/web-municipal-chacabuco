<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ObraAnexo;
use App\Models\ObraProcedimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObraProcedimientoAdminController extends Controller
{
    public function index()
    {
        $query = ObraProcedimiento::query();

        if (DB::getDriverName() === 'mysql') {
            $query->orderByRaw("FIELD(seccion,'obras','balcones','mensura','libre_deuda')");
        }

        $procedimientos = $query->orderBy('orden')->get()->groupBy('seccion');

        return view('admin.obras-particulares.procedimientos.index', [
            'procedimientos' => $procedimientos,
            'secciones'      => ObraProcedimiento::SECCIONES,
        ]);
    }

    public function edit(ObraProcedimiento $procedimiento)
    {
        $anexos = ObraAnexo::orderBy('orden')->orderBy('nombre')->get();

        return view('admin.obras-particulares.procedimientos.form', [
            'procedimiento' => $procedimiento,
            'anexos'        => $anexos,
        ]);
    }

    public function update(Request $request, ObraProcedimiento $procedimiento)
    {
        $data = $request->validate([
            'titulo'    => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'orden'     => ['nullable', 'integer', 'min:0', 'max:999'],
            'visible'   => ['nullable', 'boolean'],
        ]);

        $data['visible'] = $request->boolean('visible', true);
        $data['orden']   = (int) ($data['orden'] ?? $procedimiento->orden);

        $procedimiento->fill($data)->save();

        AuditLog::registrar('editar', 'ObraProcedimiento', $procedimiento->id, "Procedimiento editado: \"{$procedimiento->titulo}\"");

        return redirect()
            ->route('admin.obras.procedimientos.index')
            ->with('ok', 'Procedimiento actualizado correctamente.');
    }
}
