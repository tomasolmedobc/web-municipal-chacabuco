<?php

namespace Tests\Feature;

use App\Models\HabilitacionDocumento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HabilitacionesAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_habilitaciones_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($user)->post(route('admin.habilitaciones.store'), [
            'seccion' => HabilitacionDocumento::SECCION_ARCHIVOS,
            'titulo' => 'Formulario de prueba',
            'descripcion' => 'Documento para tramites.',
            'categoria' => 'Formulario',
            'estado' => 'visible',
            'orden' => 1,
            'archivo' => UploadedFile::fake()->create('formulario.pdf', 128, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.habilitaciones.index', [
            'seccion' => HabilitacionDocumento::SECCION_ARCHIVOS,
        ]));

        $documento = HabilitacionDocumento::where('titulo', 'Formulario de prueba')->first();

        $this->assertNotNull($documento);
        $this->assertSame(HabilitacionDocumento::SECCION_ARCHIVOS, $documento->seccion);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $documento->archivo_ruta));
    }

    public function test_admin_document_uses_default_description_when_empty(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $this->actingAs($user)->post(route('admin.habilitaciones.store'), [
            'seccion' => HabilitacionDocumento::SECCION_ARCHIVOS,
            'titulo' => 'Documento sin descripcion',
            'categoria' => 'Formulario',
            'estado' => 'visible',
            'orden' => 1,
            'archivo' => UploadedFile::fake()->create('documento.pdf', 128, 'application/pdf'),
        ]);

        $documento = HabilitacionDocumento::where('titulo', 'Documento sin descripcion')->first();

        $this->assertSame(
            'Documento disponible para consultar, descargar o utilizar en tramites de habilitaciones.',
            $documento->descripcion
        );
    }

    public function test_public_page_renders_admin_documents_and_searches_ordinances(): void
    {
        HabilitacionDocumento::create([
            'seccion' => HabilitacionDocumento::SECCION_ARCHIVOS,
            'titulo' => 'Formulario cargado desde admin',
            'categoria' => 'Formulario',
            'estado' => 'visible',
            'orden' => 1,
            'archivo_nombre' => 'formulario.pdf',
            'archivo_ruta' => '/storage/habilitaciones/archivos/formulario.pdf',
        ]);

        HabilitacionDocumento::create([
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
            'titulo' => 'Food truck',
            'categoria' => 'Gastronomia',
            'numero' => '7427/17',
            'anio' => 2017,
            'estado' => 'visible',
            'orden' => 1,
            'archivo_nombre' => 'food-truck.pdf',
            'archivo_ruta' => '/storage/habilitaciones/ordenanzas/food-truck.pdf',
        ]);

        HabilitacionDocumento::create([
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
            'titulo' => 'Farmacias',
            'categoria' => 'Salud',
            'numero' => '8154/19',
            'anio' => 2019,
            'estado' => 'visible',
            'orden' => 2,
            'archivo_nombre' => 'farmacias.pdf',
            'archivo_ruta' => '/storage/habilitaciones/ordenanzas/farmacias.pdf',
        ]);

        $response = $this->get(route('habilitaciones.index', ['ordenanza' => '7427']));

        $response->assertOk();
        $response->assertSee('Formulario cargado desde admin');
        $response->assertSee('Food truck');
        $response->assertSee('7427/17');
        $response->assertDontSee('Farmacias');
    }

    public function test_ordinance_uploads_are_stored_in_shared_ordenanza_folder(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $this->actingAs($user)->post(route('admin.habilitaciones.store'), [
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
            'titulo' => 'Ordenanza Food Truck',
            'categoria' => 'Gastronomia',
            'numero' => '7427/17',
            'anio' => 2017,
            'estado' => 'visible',
            'orden' => 1,
            'archivo' => UploadedFile::fake()->create('food-truck.pdf', 128, 'application/pdf'),
        ])->assertRedirect(route('admin.habilitaciones.index', [
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
        ]));

        $documento = HabilitacionDocumento::where('titulo', 'Ordenanza Food Truck')->first();

        $this->assertNotNull($documento);
        $this->assertStringStartsWith('/storage/Ordenanza/', $documento->archivo_ruta);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $documento->archivo_ruta));
    }

    public function test_admin_can_create_ordinance_with_external_link(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $this->actingAs($user)->post(route('admin.habilitaciones.store'), [
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
            'titulo' => 'Ordenanza externa',
            'categoria' => 'Comercio',
            'numero' => '1234/26',
            'anio' => 2026,
            'estado' => 'visible',
            'orden' => 1,
            'link_externo' => 'https://example.com/ordenanza-externa.pdf',
        ])->assertRedirect(route('admin.habilitaciones.index', [
            'seccion' => HabilitacionDocumento::SECCION_ORDENANZAS,
        ]));

        $response = $this->get(route('habilitaciones.index', ['ordenanza' => '1234']));

        $response->assertOk();
        $response->assertSee('Ordenanza externa');
        $response->assertSee('https://example.com/ordenanza-externa.pdf');
    }
}
