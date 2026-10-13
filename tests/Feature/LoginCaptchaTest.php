<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Login del panel: captcha (Cloudflare Turnstile), credenciales, límites y auditoría de accesos.
 */
class LoginCaptchaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $email = 'admin@chacabuco.gob.ar', string $password = 'secret123'): User
    {
        $user = User::factory()->create([
            'email'    => $email,
            'password' => Hash::make($password),
        ]);
        $user->forceFill(['rol' => 'admin'])->save();

        return $user;
    }

    private function intentar(string $email, string $password, string $token = 'token-de-prueba')
    {
        return $this->post(route('login.post'), [
            'email'                 => $email,
            'password'              => $password,
            'cf-turnstile-response' => $token,
        ]);
    }

    private function activarTurnstile(bool $cloudflareAprueba): void
    {
        config(['services.turnstile.secret_key' => 'clave-secreta-de-prueba']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => $cloudflareAprueba])]);
    }

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_login_requires_the_turnstile_token(): void
    {
        $this->admin();

        $this->post(route('login.post'), ['email' => 'admin@chacabuco.gob.ar', 'password' => 'secret123'])
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_login_fails_when_cloudflare_rejects_the_token(): void
    {
        $this->admin();
        $this->activarTurnstile(false);

        $this->intentar('admin@chacabuco.gob.ar', 'secret123')->assertSessionHasErrors('captcha');

        $this->assertGuest();
    }

    public function test_login_works_when_cloudflare_approves_the_token(): void
    {
        $user = $this->admin();
        $this->activarTurnstile(true);

        $this->intentar('admin@chacabuco.gob.ar', 'secret123')->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_credentials_and_is_audited(): void
    {
        $this->intentar('noexiste@example.com', 'wrongpassword')->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['accion' => 'login_fallido', 'modelo' => 'Acceso']);
    }

    public function test_successful_login_and_logout_are_audited(): void
    {
        $user = $this->admin();

        $this->intentar('admin@chacabuco.gob.ar', 'secret123')->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('audit_logs', ['accion' => 'login', 'user_id' => $user->id]);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertDatabaseHas('audit_logs', ['accion' => 'logout', 'user_id' => $user->id]);
    }

    public function test_account_is_locked_after_too_many_failed_attempts_for_the_same_email(): void
    {
        $this->admin();

        // Simula intentos fallidos previos desde otras IP (el límite por email es independiente del de la ruta)
        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit('login:admin@chacabuco.gob.ar', 300);
        }

        $respuesta = $this->intentar('admin@chacabuco.gob.ar', 'secret123');

        $respuesta->assertSessionHasErrors('email');
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('email'));
        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['accion' => 'login_bloqueado']);
    }
}
