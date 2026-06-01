<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccesoMunicipalController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->session()->get('acceso_municipal_autorizado')) {
            return view('acceso-municipal.login');
        }

        return view('acceso-municipal.index', [
            'links' => collect(config('acceso_municipal.links'))
                ->filter(fn ($link) => filled($link['titulo'] ?? null))
                ->values(),
        ]);
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $password = trim((string) config('acceso_municipal.password'));

        if ($password === '' || ! hash_equals($password, trim($data['password']))) {
            return back()
                ->withErrors(['password' => 'La clave ingresada no es correcta.'])
                ->onlyInput([]);
        }

        $request->session()->put('acceso_municipal_autorizado', true);

        return redirect()->route('acceso-municipal.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('acceso_municipal_autorizado');

        return redirect()->route('acceso-municipal.index');
    }
}
