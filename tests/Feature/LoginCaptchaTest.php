<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_shows_captcha_question(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('captcha');
        $response->assertSessionHas('captcha_respuesta');
    }

    public function test_login_fails_with_wrong_captcha(): void
    {
        $this->get(route('login'));

        $respuestaCorrecta = session('captcha_respuesta');
        $respuestaIncorrecta = $respuestaCorrecta + 1;

        $response = $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'password',
            'captcha'  => $respuestaIncorrecta,
        ]);

        $response->assertSessionHasErrors('captcha');
    }

    public function test_login_fails_with_missing_captcha(): void
    {
        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('captcha');
    }

    public function test_login_fails_with_wrong_credentials_even_with_correct_captcha(): void
    {
        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email'    => 'noexiste@example.com',
            'password' => 'wrongpassword',
            'captcha'  => session('captcha_respuesta'),
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_succeeds_with_correct_captcha_and_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@chacabuco.gob.ar',
            'password' => Hash::make('secret123'),
        ]);
        $user->forceFill(['rol' => 'admin'])->save();

        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email'    => 'admin@chacabuco.gob.ar',
            'password' => 'secret123',
            'captcha'  => session('captcha_respuesta'),
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_captcha_is_cleared_from_session_after_wrong_answer(): void
    {
        $this->get(route('login'));

        $respuestaCorrecta = session('captcha_respuesta');

        $this->post(route('login'), [
            'email'    => 'user@example.com',
            'password' => 'password',
            'captcha'  => $respuestaCorrecta + 1,
        ]);

        $this->assertNull(session('captcha_respuesta'));
    }

    public function test_captcha_is_cleared_from_session_after_successful_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123'),
        ]);
        $user->forceFill(['rol' => 'admin'])->save();

        $this->get(route('login'));

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'secret123',
            'captcha'  => session('captcha_respuesta'),
        ]);

        $this->assertNull(session('captcha_respuesta'));
    }
}
