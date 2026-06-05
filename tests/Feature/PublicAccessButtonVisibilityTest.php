<?php

namespace Tests\Feature;

use App\Models\PublicAccessButton;
use App\Models\User;
use App\Services\PublicAccessButtonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAccessButtonVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_visibility_panel(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($user)->get(route('admin.botones-visibilidad.index'));

        $response->assertOk();
        $response->assertSee('Visibilidad de botones');
        $response->assertSee('Tramites');
        $response->assertSee('Servicios');
        $response->assertSee('Gobierno Abierto');
        $response->assertSee('Expedientes');
        $response->assertSee('OMIC');
        $response->assertSee('Licitaciones');
    }

    public function test_editor_cannot_open_visibility_panel(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'editor'])->save();

        $this->actingAs($user)
            ->get(route('admin.botones-visibilidad.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->put(route('admin.botones-visibilidad.update'), [])
            ->assertForbidden();
    }

    public function test_visibility_button_is_only_visible_to_admin_on_profile(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['rol' => 'admin'])->save();

        $adminResponse = $this->actingAs($admin)->get(route('admin.perfil.edit'));

        $adminResponse->assertOk();
        $adminResponse->assertSee('Visibilidad botones');
        $adminResponse->assertSee(route('admin.botones-visibilidad.index'));

        $editor = User::factory()->create();
        $editor->forceFill(['rol' => 'editor'])->save();

        $editorResponse = $this->actingAs($editor)->get(route('admin.perfil.edit'));

        $editorResponse->assertOk();
        $editorResponse->assertDontSee('Visibilidad botones');
        $editorResponse->assertDontSee(route('admin.botones-visibilidad.index'));
    }

    public function test_visibility_button_is_not_shown_on_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Visibilidad botones');
    }

    public function test_hidden_tramites_button_is_not_rendered_publicly(): void
    {
        $this->get(route('tramites-servicios.index'))->assertOk();

        PublicAccessButton::where('seccion', PublicAccessButtonService::SECCION_TRAMITES)
            ->where('clave', 'expedientes')
            ->update(['activo' => false]);

        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertDontSee('Expedientes');
        $response->assertSee('Habilitaciones');
    }

    public function test_hidden_gobierno_abierto_button_is_not_rendered_publicly(): void
    {
        $this->get(route('gobierno-abierto.index'))->assertOk();

        PublicAccessButton::where('seccion', PublicAccessButtonService::SECCION_GOBIERNO_ABIERTO)
            ->where('clave', 'consulta-proveedores')
            ->update(['activo' => false]);

        $response = $this->get(route('gobierno-abierto.index'));

        $response->assertOk();
        $response->assertDontSee('Consulta Proveedores');
        $response->assertSee('Licitaciones');
    }

    public function test_admin_update_changes_button_visibility_without_deleting_it(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $this->actingAs($user)->get(route('admin.botones-visibilidad.index'))->assertOk();

        $expedientes = PublicAccessButton::where('clave', 'expedientes')->firstOrFail();
        $omic = PublicAccessButton::where('clave', 'omic')->firstOrFail();

        $response = $this->actingAs($user)->put(route('admin.botones-visibilidad.update'), [
            'activos' => [
                (string) $omic->id => '1',
            ],
        ]);

        $response->assertRedirect(route('admin.botones-visibilidad.index'));

        $this->assertDatabaseHas('public_access_buttons', [
            'id' => $expedientes->id,
            'activo' => false,
        ]);

        $this->assertDatabaseHas('public_access_buttons', [
            'id' => $omic->id,
            'activo' => true,
        ]);
    }

    public function test_admin_can_customize_button_link(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $this->actingAs($user)->get(route('admin.botones-visibilidad.index'))->assertOk();

        $expedientes = PublicAccessButton::where('clave', 'expedientes')->firstOrFail();
        $customUrl = 'https://example.com/expedientes-nuevo';

        $response = $this->actingAs($user)->put(route('admin.botones-visibilidad.update'), [
            'activos' => [
                (string) $expedientes->id => '1',
            ],
            'links' => [
                (string) $expedientes->id => $customUrl,
            ],
        ]);

        $response->assertRedirect(route('admin.botones-visibilidad.index'));

        $this->assertDatabaseHas('public_access_buttons', [
            'id' => $expedientes->id,
            'url_personalizada' => $customUrl,
        ]);

        $this->get(route('tramites-servicios.index'))
            ->assertOk()
            ->assertSee($customUrl);
    }
}
