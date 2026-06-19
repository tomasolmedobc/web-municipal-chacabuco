<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccesoMunicipalController extends Controller
{
    private const SESSION_TTL_HORAS = 8;

    public function show(Request $request)
    {
        if (! $this->sesionValida($request)) {
            $this->limpiarSesion($request);
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
        $request->session()->put('acceso_municipal_expira', now()->addHours(self::SESSION_TTL_HORAS)->timestamp);

        return redirect()->route('acceso-municipal.index');
    }

    public function logout(Request $request)
    {
        $this->limpiarSesion($request);

        return redirect()->route('acceso-municipal.index');
    }

    private function sesionValida(Request $request): bool
    {
        if (! $request->session()->get('acceso_municipal_autorizado')) {
            return false;
        }

        $expira = $request->session()->get('acceso_municipal_expira', 0);

        return now()->timestamp <= $expira;
    }

    private function limpiarSesion(Request $request): void
    {
        $request->session()->forget(['acceso_municipal_autorizado', 'acceso_municipal_expira']);
    }
}
