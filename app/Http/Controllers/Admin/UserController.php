<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::latest()->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $nuevoUsuario = User::create([
            'name'     => $data['name'],
            'apellido' => $data['apellido'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'rol'      => $data['rol'],
        ]);

        AuditLog::registrar('crear', 'User', $nuevoUsuario->id, "Usuario creado: \"{$data['name']}\" ({$data['email']}) — rol: {$data['rol']}");

        return redirect()
            ->route('admin.usuarios.index')
            ->with('ok', 'Usuario creado correctamente');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUserRequest $request, User $usuario)
    {
        $data = $request->validated();

        if (auth()->id() === $usuario->id && $data['rol'] !== 'admin') {
            return back()->with('error', 'No podés quitarte tu propio rol de admin');
        }

        $usuario->name     = $data['name'];
        $usuario->apellido = $data['apellido'];
        $usuario->email    = $data['email'];
        $usuario->rol      = $data['rol'];

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        AuditLog::registrar('editar', 'User', $usuario->id, "Usuario editado: \"{$usuario->name}\" ({$usuario->email}) — rol: {$usuario->rol}");

        return back()->with('ok', 'Usuario actualizado correctamente');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No podés eliminarte a vos mismo');
        }

        AuditLog::registrar('eliminar', 'User', $usuario->id, "Usuario eliminado: \"{$usuario->name}\" ({$usuario->email})");

        $usuario->delete();

        return back()->with('ok', 'Usuario eliminado correctamente');
    }

    public function resetPassword(User $usuario)
    {
        $nuevaPassword = Str::random(12);

        $usuario->password = Hash::make($nuevaPassword);
        $usuario->save();

        AuditLog::registrar('editar', 'User', $usuario->id, "Contraseña reseteada para: \"{$usuario->name}\" ({$usuario->email})");

        return back()->with('password_generada', $nuevaPassword);
    }
}