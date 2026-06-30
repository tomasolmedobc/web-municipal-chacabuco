<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $a = rand(1, 9);
        $b = rand(1, 9);
        session(['captcha_respuesta' => $a + $b]);

        return view('auth.login', ['captcha_pregunta' => "$a + $b"]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'captcha'  => ['required', 'numeric'],
        ]);

        if ((int) $request->input('captcha') !== (int) session('captcha_respuesta')) {
            session()->forget('captcha_respuesta');
            return back()->withErrors(['captcha' => 'Respuesta incorrecta.'])->onlyInput('email');
        }

        session()->forget('captcha_respuesta');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}