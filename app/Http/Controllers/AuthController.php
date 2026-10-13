<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

        // Límite por email (además del throttle por IP de la ruta): frena ataques distribuidos a una cuenta
        $clave = 'login:' . Str::lower($request->input('email'));

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            $segundos = RateLimiter::availableIn($clave);

            AuditLog::registrarAcceso('login_bloqueado', 'Cuenta bloqueada por demasiados intentos: ' . Str::limit($request->input('email'), 100));

            return back()->withErrors([
                'email' => "Demasiados intentos. Probá de nuevo en {$segundos} segundos.",
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($clave);
            $request->session()->regenerate();

            AuditLog::registrarAcceso('login', 'Inicio de sesión', Auth::user());

            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($clave, 300);

        AuditLog::registrarAcceso('login_fallido', 'Intento de acceso fallido con el correo: ' . Str::limit($request->input('email'), 100));

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $usuario = Auth::user();

        if ($usuario) {
            AuditLog::registrarAcceso('logout', 'Cierre de sesión', $usuario);
        }

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