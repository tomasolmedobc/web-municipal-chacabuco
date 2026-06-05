<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReclamosTest extends TestCase
{
    use RefreshDatabase;

    public function test_reclamos_button_points_to_public_page(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('/tramites-y-servicios/reclamos');
    }

    public function test_reclamos_page_renders_when_local_database_is_available(): void
    {
        $this->skipIfReclamosDatabaseIsUnavailable();

        $response = $this->get(route('reclamos.index'));

        $response->assertOk();
        $response->assertSee('Gestion de Reclamos');
        $response->assertSee('Nuevo reclamo');
        $response->assertSee('Consultar reclamo');
    }

    public function test_reclamos_query_works_against_local_database_when_available(): void
    {
        $this->skipIfReclamosDatabaseIsUnavailable();

        $codigo = DB::connection('reclamos')
            ->table('reclamos')
            ->orderByDesc('id_reclamo')
            ->value('id_reclamo');

        if (! $codigo) {
            $this->markTestSkipped('La base local de reclamos no tiene reclamos cargados.');
        }

        $response = $this->followingRedirects()->post(route('reclamos.consultar'), [
            'idreclamo' => $codigo,
        ]);

        $response->assertOk();
        $response->assertSee((string) $codigo);
        $response->assertSee('Informacion de su Reclamo');

        $refresh = $this->get(route('reclamos.index'));

        $refresh->assertOk();
        $refresh->assertDontSee('Informacion de su Reclamo');
    }

    public function test_reclamos_store_validates_required_fields(): void
    {
        $response = $this->post(route('reclamos.store'), []);

        $response->assertSessionHasErrors([
            'apellido',
            'nombre',
            'telefono',
            'dni',
            'area',
            'tipo',
            'detalles',
        ]);
    }

    private function skipIfReclamosDatabaseIsUnavailable(): void
    {
        try {
            DB::connection('reclamos')->table('areas')->limit(1)->exists();
        } catch (\Throwable) {
            $this->markTestSkipped('La base local de reclamos no esta disponible.');
        }
    }
}
