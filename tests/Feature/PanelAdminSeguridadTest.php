<?php

namespace Tests\Feature;

use App\Models\PublicAccessButton;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Permisos del panel, contraseña actual en el perfil, cierre de sesiones y validación de links.
 */
class PanelAdminSeguridadTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol = 'admin', array $modulos = []): User
    {
        $user = User::factory()->create(['password' => Hash::make('clave-actual-123')]);
        $user->forceFill(['rol' => $rol, 'modulos' => $rol === 'admin' ? null : $modulos])->save();

        return $user;
    }

    public function test_admin_routes_require_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.noticias.index'))->assertRedirect(route('login'));
    }

    public function test_editor_cannot_open_a_module_that_was_not_assigned(): void
    {
        $editor = $this->usuario('editor', ['noticias']);

        $this->actingAs($editor)->get(route('admin.noticias.index'))->assertOk();
        $this->actingAs($editor)->get(route('admin.tasas.index'))->assertForbidden();
    }

    public function test_editor_cannot_manage_users_or_system_settings(): void
    {
        $editor = $this->usuario('editor', ['noticias']);

        $this->actingAs($editor)->get(route('admin.usuarios.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.sistema.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.botones-visibilidad.index'))->assertForbidden();
    }

    public function test_profile_requires_the_current_password_to_change_it(): void
    {
        $user = $this->usuario();
        $nueva = ['name' => 'Nombre', 'password' => 'otra-clave-12345', 'password_confirmation' => 'otra-clave-12345'];

        $this->actingAs($user)->put(route('admin.perfil.update'), $nueva)
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('admin.perfil.update'), $nueva + ['current_password' => 'incorrecta'])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put(route('admin.perfil.update'), $nueva + ['current_password' => 'clave-actual-123'])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('otra-clave-12345', $user->fresh()->password));
    }

    public function test_profile_allows_changing_the_name_without_the_password(): void
    {
        $user = $this->usuario();

        $this->actingAs($user)->put(route('admin.perfil.update'), ['name' => 'Otro Nombre'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Otro Nombre', $user->fresh()->name);
    }

    public function test_new_passwords_need_at_least_ten_characters(): void
    {
        $user = $this->usuario();

        $this->actingAs($user)->put(route('admin.perfil.update'), [
            'name' => 'N', 'current_password' => 'clave-actual-123',
            'password' => 'corta123', 'password_confirmation' => 'corta123',
        ])->assertSessionHasErrors('password');
    }

    public function test_closing_sessions_keeps_only_the_excepted_one(): void
    {
        config(['session.driver' => 'database']);
        $user = $this->usuario();
        $otro = $this->usuario();

        $fila = fn (string $id, ?int $uid) => [
            'id' => $id, 'user_id' => $uid, 'ip_address' => '127.0.0.1',
            'user_agent' => 'test', 'payload' => 'x', 'last_activity' => time(),
        ];
        DB::table('sessions')->insert([$fila('actual', $user->id), $fila('vieja', $user->id), $fila('ajena', $otro->id)]);

        $user->cerrarSesiones('actual');

        $this->assertEqualsCanonicalizing(['actual', 'ajena'], DB::table('sessions')->pluck('id')->all());

        $user->cerrarSesiones();

        $this->assertSame(['ajena'], DB::table('sessions')->pluck('id')->all());
    }

    public function test_button_custom_links_reject_dangerous_schemes(): void
    {
        $admin = $this->usuario();
        $this->actingAs($admin)->get(route('admin.botones-visibilidad.index'))->assertOk(); // sincroniza los botones
        $boton = PublicAccessButton::firstOrFail();

        foreach (['javascript:alert(1)', 'data:text/html,x', '//otro-dominio.com', 'sin-protocolo.com'] as $malo) {
            $this->actingAs($admin)->put(route('admin.botones-visibilidad.update'), ['links' => [$boton->id => $malo]])
                ->assertSessionHasErrors("links.{$boton->id}");
        }

        foreach (['https://sibom.slyt.gba.gob.ar/', '/tasas-municipales'] as $bueno) {
            $this->actingAs($admin)->put(route('admin.botones-visibilidad.update'), ['links' => [$boton->id => $bueno]])
                ->assertSessionHasNoErrors();
        }
    }
}
