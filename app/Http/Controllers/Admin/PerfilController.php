<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PerfilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        $usuario = auth()->user();

        return view('admin.perfil.edit', compact('usuario'));
    }

    public function update(PerfilRequest $request)
    {
        $usuario = auth()->user();

        $data = $request->validated();

        $usuario->name = $data['name'];

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $usuario->save();

        return back()->with('ok', 'Perfil actualizado correctamente');
    }
}