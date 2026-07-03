<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TasasCuota;
use App\Models\TasasGrupo;
use Illuminate\Http\Request;

class TasasCuotaAdminController extends Controller
{
    public function index(Request $request)
    {
        $grupoId = $request->query('grupo_id');

        $cuotas = TasasCuota::with('grupo')
            ->when($grupoId, fn($q) => $q->where('grupo_id', $grupoId))
            ->orderBy('grupo_id')
            ->orderBy('orden')
            ->paginate(50)
            ->withQueryString();

        return view('admin.tasas.cuotas.index', [
            'cuotas' => $cuotas,
            'grupos' => TasasGrupo::orderBy('orden')->get(),
            'grupoId' => $grupoId,
        ]);
    }

    public function create()
    {
        return view('admin.tasas.cuotas.form', [
            'cuota'  => null,
            'grupos' => TasasGrupo::orderBy('orden')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo_id'          => ['required', 'exists:tasas_grupos,id'],
            'cuota_label'       => ['required', 'string', 'max:100'],
            'fecha_vencimiento' => ['required', 'date'],
            'orden'             => ['required', 'integer', 'min:0', 'max:999'],
            'estado'            => ['required', 'in:visible,oculto'],
        ]);

        $cuota = TasasCuota::create($data);

        AuditLog::registrar('crear', 'TasasCuota', $cuota->id, "Cuota creada: {$cuota->cuota_label}");

        return redirect()->route('admin.tasas.cuotas.index', ['grupo_id' => $cuota->grupo_id])
            ->with('ok', 'Cuota creada correctamente.');
    }

    public function edit(TasasCuota $cuota)
    {
        return view('admin.tasas.cuotas.form', [
            'cuota'  => $cuota,
            'grupos' => TasasGrupo::orderBy('orden')->get(),
        ]);
    }

    public function update(Request $request, TasasCuota $cuota)
    {
        $data = $request->validate([
            'grupo_id'          => ['required', 'exists:tasas_grupos,id'],
            'cuota_label'       => ['required', 'string', 'max:100'],
            'fecha_vencimiento' => ['required', 'date'],
            'orden'             => ['required', 'integer', 'min:0', 'max:999'],
            'estado'            => ['required', 'in:visible,oculto'],
        ]);

        $cuota->update($data);

        AuditLog::registrar('editar', 'TasasCuota', $cuota->id, "Cuota editada: {$cuota->cuota_label}");

        return redirect()->route('admin.tasas.cuotas.index', ['grupo_id' => $cuota->grupo_id])
            ->with('ok', 'Cuota actualizada correctamente.');
    }

    public function destroy(TasasCuota $cuota)
    {
        $grupoId = $cuota->grupo_id;
        $label   = $cuota->cuota_label;
        $id      = $cuota->id;

        AuditLog::registrar('eliminar', 'TasasCuota', $id, "Cuota eliminada: {$label}");
        $cuota->delete();

        return redirect()->route('admin.tasas.cuotas.index', ['grupo_id' => $grupoId])
            ->with('ok', "Cuota \"{$label}\" eliminada.");
    }
}
