<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'rol' => 'required|in:admin,editor',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol' => $data['rol'],
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('ok', 'Usuario creado correctamente');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$usuario->id}",
            'rol' => 'required|in:admin,editor',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (auth()->id() === $usuario->id && $data['rol'] !== 'admin') {
            return back()->with('error', 'No podés quitarte tu propio rol de admin');
        }

        $usuario->name = $data['name'];
        $usuario->email = $data['email'];
        $usuario->rol = $data['rol'];

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        return back()->with('ok', 'Usuario actualizado correctamente');
    }

    public function destroy(User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No podés eliminarte a vos mismo');
        }

        $usuario->delete();

        return back()->with('ok', 'Usuario eliminado correctamente');
    }

    public function resetPassword(User $usuario)
    {
        $nuevaPassword = Str::random(8);

        $usuario->password = Hash::make($nuevaPassword);
        $usuario->save();

        return back()->with('ok', "Nueva contraseña: {$nuevaPassword}");
    }
}