<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        $token = $request->input('cf-turnstile-response');

        $verified = $this->verifyTurnstile($token, $request->ip());

        if (! $verified) {
            return back()->withErrors(['captcha' => 'Verificación de seguridad fallida. Intentá de nuevo.'])->onlyInput('email');
        }

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

    private function verifyTurnstile(string $token, string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return true;
        }

        try {
            $response = Http::asForm()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                ['secret' => $secret, 'response' => $token, 'remoteip' => $ip]
            );

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable) {
            return false;
        }
    }
}