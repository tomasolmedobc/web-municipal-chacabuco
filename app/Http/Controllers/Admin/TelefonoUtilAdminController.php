<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TelefonoUtil;
use App\Http\Requests\Admin\TelefonoUtilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelefonoUtilAdminController extends Controller
{
    public function index(Request $request)
    {
        $q         = trim($request->input('q', ''));
        $categoria = $request->input('categoria', '');

        $query = TelefonoUtil::query();

        if (DB::getDriverName() === 'mysql') {
            $query->orderByRaw("FIELD(categoria, '" . implode("','", TelefonoUtil::CATEGORIAS) . "')");
        }

        $query->orderBy('orden')->orderBy('nombre');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre',    'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('email',    'like', "%{$q}%")
                    ->orWhere('direccion','like', "%{$q}%");
            });
        }

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        $telefonos = $query->paginate(25)->withQueryString();

        return view('admin.telefonos-utiles.index', [
            'telefonos'  => $telefonos,
            'categorias' => TelefonoUtil::CATEGORIAS,
            'q'          => $q,
            'categoria'  => $categoria,
        ]);
    }

    public function create()
    {
        return view('admin.telefonos-utiles.form', [
            'item'       => new TelefonoUtil(['estado' => 'visible', 'orden' => 0]),
            'categorias' => TelefonoUtil::CATEGORIAS,
            'modo'       => 'crear',
        ]);
    }

    public function store(TelefonoUtilRequest $request)
    {
        $data = $request->validated();
        $item = TelefonoUtil::create($data);

        AuditLog::registrar('crear', 'TelefonoUtil', $item->id, $item->nombre);

        return redirect()->route('admin.telefonos-utiles.index')
            ->with('ok', 'Entrada creada correctamente.');
    }

    public function edit(TelefonoUtil $telefonoUtil)
    {
        return view('admin.telefonos-utiles.form', [
            'item'       => $telefonoUtil,
            'categorias' => TelefonoUtil::CATEGORIAS,
            'modo'       => 'editar',
        ]);
    }

    public function update(TelefonoUtilRequest $request, TelefonoUtil $telefonoUtil)
    {
        $data = $request->validated();
        $telefonoUtil->update($data);

        AuditLog::registrar('editar', 'TelefonoUtil', $telefonoUtil->id, $telefonoUtil->nombre);

        return redirect()->route('admin.telefonos-utiles.index')
            ->with('ok', 'Entrada actualizada correctamente.');
    }

    public function destroy(TelefonoUtil $telefonoUtil)
    {
        $nombre = $telefonoUtil->nombre;
        $id     = $telefonoUtil->id;
        $telefonoUtil->delete();

        AuditLog::registrar('eliminar', 'TelefonoUtil', $id, $nombre);

        return redirect()->route('admin.telefonos-utiles.index')
            ->with('ok', 'Entrada eliminada.');
    }

}
