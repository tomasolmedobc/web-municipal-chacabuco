<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BaileUsuario;
use App\Http\Requests\Admin\BaileUsuarioRequest;
use Illuminate\Http\Request;

class BaileUsuariosAdminController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('q');

        $usuarios = BaileUsuario::query()
            ->when($busqueda, fn ($q) => $q->where('nombre_completo', 'like', "%{$busqueda}%")
                ->orWhere('dni', 'like', "%{$busqueda}%"))
            ->withCount('reservas')
            ->orderBy('nombre_completo')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.baile-egresados.usuarios.index', compact('usuarios', 'busqueda'));
    }

    public function create()
    {
        return view('admin.baile-egresados.usuarios.form', [
            'usuario' => new BaileUsuario(['disponibles' => 2]),
            'modo'    => 'crear',
        ]);
    }

    public function store(BaileUsuarioRequest $request)
    {
        $data = $request->validated();
        $usuario = BaileUsuario::create($data);

        AuditLog::registrar('crear', 'BaileUsuario', $usuario->id, "Usuario baile creado: {$usuario->nombre_completo} (DNI: {$usuario->dni})");

        return redirect()->route('admin.baile.usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(BaileUsuario $usuario)
    {
        return view('admin.baile-egresados.usuarios.form', [
            'usuario' => $usuario,
            'modo'    => 'editar',
        ]);
    }

    public function update(BaileUsuarioRequest $request, BaileUsuario $usuario)
    {
        $data = $request->validated();
        $usuario->fill($data)->save();

        AuditLog::registrar('editar', 'BaileUsuario', $usuario->id, "Usuario baile editado: {$usuario->nombre_completo}");

        return redirect()->route('admin.baile.usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(BaileUsuario $usuario)
    {
        AuditLog::registrar('eliminar', 'BaileUsuario', $usuario->id, "Usuario baile eliminado: {$usuario->nombre_completo} (DNI: {$usuario->dni})");
        $usuario->delete();

        return redirect()->route('admin.baile.usuarios.index')->with('ok', 'Usuario eliminado correctamente.');
    }

}
