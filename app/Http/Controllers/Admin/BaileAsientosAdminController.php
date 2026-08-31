<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BaileLugar;
use App\Http\Requests\Admin\BaileAsientoRequest;
use Illuminate\Http\Request;

class BaileAsientosAdminController extends Controller
{
    public function index(Request $request)
    {
        $filtro   = $request->get('disponible', '');
        $busqueda = $request->get('q');

        $asientos = BaileLugar::query()
            ->when($busqueda, fn ($q) => $q->where('color', 'like', "%{$busqueda}%")
                ->orWhere('fila', 'like', "%{$busqueda}%"))
            ->when($filtro !== '', fn ($q) => $q->where('disponible', (bool) $filtro))
            ->orderBy('color')
            ->orderBy('fila')
            ->orderBy('numero')
            ->paginate(30)
            ->appends($request->query());

        $totales = [
            'total'      => BaileLugar::count(),
            'disponible' => BaileLugar::where('disponible', true)->count(),
            'reservado'  => BaileLugar::where('disponible', false)->count(),
        ];

        return view('admin.baile-egresados.asientos.index', compact('asientos', 'busqueda', 'filtro', 'totales'));
    }

    public function create()
    {
        return view('admin.baile-egresados.asientos.form', [
            'asiento' => new BaileLugar(['disponible' => true]),
            'modo'    => 'crear',
        ]);
    }

    public function store(BaileAsientoRequest $request)
    {
        $data = $request->validated();
        $data['disponible'] = $request->boolean('disponible');
        $asiento = BaileLugar::create($data);

        AuditLog::registrar('crear', 'BaileLugar', $asiento->id, "Asiento creado: {$asiento->descripcion}");

        return redirect()->route('admin.baile.asientos.index')->with('ok', 'Asiento creado correctamente.');
    }

    public function edit(BaileLugar $asiento)
    {
        return view('admin.baile-egresados.asientos.form', [
            'asiento' => $asiento,
            'modo'    => 'editar',
        ]);
    }

    public function update(BaileAsientoRequest $request, BaileLugar $asiento)
    {
        $data = $request->validated();
        $data['disponible'] = $request->boolean('disponible');
        $asiento->fill($data)->save();

        AuditLog::registrar('editar', 'BaileLugar', $asiento->id, "Asiento editado: {$asiento->descripcion}");

        return redirect()->route('admin.baile.asientos.index')->with('ok', 'Asiento actualizado correctamente.');
    }

    public function destroy(BaileLugar $asiento)
    {
        AuditLog::registrar('eliminar', 'BaileLugar', $asiento->id, "Asiento eliminado: {$asiento->descripcion}");
        $asiento->delete();

        return redirect()->route('admin.baile.asientos.index')->with('ok', 'Asiento eliminado correctamente.');
    }

    public function storeMasivo(Request $request)
    {
        $data = $request->validate([
            'color'      => ['required', 'string', 'max:50'],
            'fila_desde' => ['required', 'string', 'max:10'],
            'fila_hasta' => ['required', 'string', 'max:10'],
            'num_desde'  => ['required', 'integer', 'min:1', 'max:999'],
            'num_hasta'  => ['required', 'integer', 'min:1', 'max:999', 'gte:num_desde'],
        ]);

        $filas = range(strtoupper($data['fila_desde']), strtoupper($data['fila_hasta']));
        $numeros = range((int) $data['num_desde'], (int) $data['num_hasta']);
        $creados = 0;
        $omitidos = 0;

        foreach ($filas as $fila) {
            foreach ($numeros as $numero) {
                $existe = BaileLugar::where('fila', $fila)->where('numero', $numero)->exists();
                if ($existe) {
                    $omitidos++;
                    continue;
                }
                BaileLugar::create([
                    'color'     => $data['color'],
                    'fila'      => $fila,
                    'numero'    => $numero,
                    'disponible' => true,
                ]);
                $creados++;
            }
        }

        AuditLog::registrar('crear', 'BaileLugar', null, "Carga masiva: {$creados} asientos creados, {$omitidos} omitidos ({$data['color']}, filas {$data['fila_desde']}–{$data['fila_hasta']}, nros {$data['num_desde']}–{$data['num_hasta']})");

        $msg = "{$creados} asiento(s) creado(s)";
        if ($omitidos) $msg .= " ({$omitidos} ya existían y se omitieron)";

        return redirect()->route('admin.baile.asientos.index')->with('ok', $msg . '.');
    }

}
