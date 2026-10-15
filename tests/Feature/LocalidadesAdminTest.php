<?php

namespace Tests\Feature;

use App\Models\Localidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Eliminación de localidades del módulo Turismo.
 */
class LocalidadesAdminTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol = 'admin', array $modulos = []): User
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => $rol, 'modulos' => $rol === 'admin' ? null : $modulos])->save();

        return $user;
    }

    private function localidad(array $extra = []): Localidad
    {
        static $n = 0;
        $n++;

        return Localidad::create($extra + [
            'nombre'      => "Localidad de prueba {$n}",
            'slug'        => "localidad-de-prueba-{$n}",
            'es_cabecera' => false,
            'orden'       => $n,
            'estado'      => 'visible',
        ]);
    }

    private function agregarAtractivos(Localidad $localidad, int $cantidad): void
    {
        for ($i = 1; $i <= $cantidad; $i++) {
            DB::table('turismo_items')->insert([
                'tipo'         => 'atractivo',
                'localidad_id' => $localidad->id,
                'titulo'       => "Atractivo {$i}",
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function test_an_empty_locality_can_be_deleted_and_is_audited(): void
    {
        $localidad = $this->localidad();

        $this->actingAs($this->usuario())
            ->delete(route('admin.turismo.localidades.destroy', $localidad))
            ->assertRedirect(route('admin.turismo.localidades.index'))
            ->assertSessionHas('ok');

        $this->assertDatabaseMissing('localidades', ['id' => $localidad->id]);
        $this->assertDatabaseHas('audit_logs', ['accion' => 'eliminar', 'modelo' => 'Localidad', 'modelo_id' => $localidad->id]);
    }

    public function test_its_cover_image_is_removed_from_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('turismo/localidades/portada.webp', 'x');
        $localidad = $this->localidad(['imagen_portada' => '/storage/turismo/localidades/portada.webp']);

        $this->actingAs($this->usuario())->delete(route('admin.turismo.localidades.destroy', $localidad));

        Storage::disk('public')->assertMissing('turismo/localidades/portada.webp');
    }

    public function test_a_locality_with_content_is_not_deleted(): void
    {
        $localidad = $this->localidad();
        $this->agregarAtractivos($localidad, 2);

        $this->actingAs($this->usuario())
            ->delete(route('admin.turismo.localidades.destroy', $localidad))
            ->assertSessionHas('error');

        $this->assertStringContainsString('2 atractivo', session('error'));
        $this->assertDatabaseHas('localidades', ['id' => $localidad->id]);
        $this->assertSame(2, DB::table('turismo_items')->where('localidad_id', $localidad->id)->count());
    }

    public function test_the_head_city_cannot_be_deleted(): void
    {
        $cabecera = $this->localidad(['es_cabecera' => true]);

        $this->actingAs($this->usuario())
            ->delete(route('admin.turismo.localidades.destroy', $cabecera))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('localidades', ['id' => $cabecera->id]);
    }

    public function test_an_editor_without_the_turismo_module_cannot_delete(): void
    {
        $localidad = $this->localidad();

        $this->actingAs($this->usuario('editor', ['noticias']))
            ->delete(route('admin.turismo.localidades.destroy', $localidad))
            ->assertForbidden();

        $this->assertDatabaseHas('localidades', ['id' => $localidad->id]);
    }

    public function test_an_editor_with_the_turismo_module_can_delete(): void
    {
        $localidad = $this->localidad();

        $this->actingAs($this->usuario('editor', ['turismo']))
            ->delete(route('admin.turismo.localidades.destroy', $localidad))
            ->assertSessionHas('ok');

        $this->assertDatabaseMissing('localidades', ['id' => $localidad->id]);
    }

    public function test_the_list_offers_delete_only_where_it_applies(): void
    {
        $cabecera = $this->localidad(['es_cabecera' => true]);
        $vacia = $this->localidad();
        $conContenido = $this->localidad();
        $this->agregarAtractivos($conContenido, 1);

        $html = $this->actingAs($this->usuario())
            ->get(route('admin.turismo.localidades.index'))
            ->assertOk()
            ->getContent();

        // Se busca el action exacto del formulario (la URL de borrar es prefijo de la de editar)
        $accion = fn (Localidad $l) => 'action="' . route('admin.turismo.localidades.destroy', $l) . '"';

        $this->assertStringNotContainsString($accion($cabecera), $html);
        $this->assertStringContainsString($accion($vacia), $html);
        $this->assertStringContainsString($accion($conContenido), $html);
        $this->assertStringContainsString('1 atractivo(s) o evento(s)', $html);
    }
}
