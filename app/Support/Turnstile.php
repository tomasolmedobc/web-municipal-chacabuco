<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Turnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function habilitado(): bool
    {
        return filled(config('services.turnstile.secret_key'));
    }

    public static function verificar(?string $token, ?string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post(self::VERIFY_URL, [
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo verificar Turnstile.', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Corta la petición con un error de validación si el captcha no es válido.
     * $campoError permite mostrar el mensaje junto al widget correcto cuando
     * una misma página tiene más de un formulario con captcha.
     */
    public static function exigir(Request $request, string $campoError = 'cf-turnstile-response'): void
    {
        if (self::verificar($request->input('cf-turnstile-response'), $request->ip())) {
            return;
        }

        throw ValidationException::withMessages([
            $campoError => 'No pudimos verificar que no sos un robot. Completá la verificación e intentá de nuevo.',
        ]);
    }
}
