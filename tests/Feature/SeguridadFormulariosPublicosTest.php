<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Captcha (Turnstile) y límites de intentos de los formularios públicos.
 */
class SeguridadFormulariosPublicosTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_form_is_rejected_without_captcha_when_turnstile_is_enabled(): void
    {
        config(['services.turnstile.secret_key' => 'clave-secreta-de-prueba']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        $this->post(route('reclamos.store'), ['apellido' => 'Perez'])
            ->assertSessionHasErrors('cf-turnstile-response');
    }

    public function test_public_form_reaches_validation_when_cloudflare_approves(): void
    {
        config(['services.turnstile.secret_key' => 'clave-secreta-de-prueba']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->post(route('reclamos.store'), ['cf-turnstile-response' => 'token-ok'])
            ->assertSessionHasErrors(['apellido', 'nombre', 'dni'])
            ->assertSessionDoesntHaveErrors('cf-turnstile-response');
    }

    /**
     * Regresión: buscar sin texto o con 1-2 letras devolvía 500 (se llamaba total() sobre una colección simple).
     */
    public function test_search_page_works_with_empty_or_short_queries(): void
    {
        $this->get(route('busqueda'))->assertOk();
        $this->get(route('busqueda', ['q' => '']))->assertOk();
        $this->get(route('busqueda', ['q' => 'a']))->assertOk();
        $this->get(route('busqueda', ['q' => 'ab']))->assertOk();
        $this->get(route('busqueda', ['q' => 'hospital']))->assertOk();
    }

    public function test_libre_deuda_requires_a_prior_dni_query(): void
    {
        $this->post(route('infracciones.libre-deuda'), [
            'dni'      => '12345678',
            'apellido' => 'Perez',
            'nombre'   => 'Juan',
        ])->assertForbidden();
    }

    public function test_infracciones_query_requires_at_least_five_characters(): void
    {
        $this->post(route('infracciones.consultar'), ['criterio' => 'dominio', 'valor' => 'AB'])
            ->assertSessionHasErrors('valor');
    }

    /**
     * Regresión: con "throttle:N,M" sin prefijo todas las rutas compartían un solo contador por IP,
     * y unas pocas búsquedas bloqueaban el login o el envío de reclamos.
     */
    public function test_rate_limits_are_independent_per_route(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->get(route('busqueda', ['q' => 'a']))->assertOk();
        }

        // El límite de recuperar contraseña es 5/min: no debe verse afectado por las 6 búsquedas
        $this->post(route('password.email'), ['email' => 'alguien@example.com'])->assertSessionHasNoErrors();
    }

    public function test_each_route_still_enforces_its_own_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('password.email'), ['email' => 'alguien@example.com'])->assertStatus(302);
        }

        $this->post(route('password.email'), ['email' => 'alguien@example.com'])->assertStatus(429);
    }

    public function test_error_pages_are_rendered_in_spanish(): void
    {
        foreach ([419 => 'La página venció', 429 => 'Demasiados intentos', 503 => 'Sitio en mantenimiento'] as $codigo => $titulo) {
            $this->assertStringContainsString($titulo, view("errors.$codigo")->render());
        }
    }
}
