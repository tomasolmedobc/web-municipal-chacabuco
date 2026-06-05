<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OmicTest extends TestCase
{
    use RefreshDatabase;

    public function test_omic_button_points_to_public_page(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('/tramites-y-servicios/omic');
    }

    public function test_omic_page_renders_even_when_local_tables_are_pending(): void
    {
        $response = $this->get(route('omic.index'));

        $response->assertOk();
        $response->assertSee('OMIC');
        $response->assertSee('Datos del reclamante');
        $response->assertSee('Datos del denunciado 1');
    }

    public function test_omic_store_validates_required_fields(): void
    {
        $response = $this->post(route('omic.store'), []);

        $response->assertSessionHasErrors([
            'apellido',
            'nombre',
            'dni',
            'nacimiento',
            'celular',
            'email',
            'direccion',
            'localidad',
            'nombre1',
            'localidad1',
            'reclamo1',
            'pretension1',
        ]);
    }

    public function test_omic_local_database_schema_can_be_reached_when_available(): void
    {
        try {
            DB::connection('omic')->table('localidades')->limit(1)->exists();
        } catch (\Throwable) {
            $this->markTestSkipped('La base local de OMIC no tiene las tablas importadas todavia.');
        }

        $this->assertTrue(true);
    }
}
