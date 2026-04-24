<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        $usuario = auth()->user();

        return view('admin.perfil.edit', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $usuario->name = $data['name'];

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        return back()->with('ok', 'Perfil actualizado correctamente');
    }
}