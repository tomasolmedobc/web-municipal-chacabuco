<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TramitesServiciosTest extends TestCase
{
    use RefreshDatabase;

    public function test_tramites_y_servicios_page_renders_tabs_and_cards(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('Tramites y Servicios');
        $response->assertSee('Seguridad e Higiene');
        $response->assertSee('Oficios Judiciales');
        $response->assertSee('Telemedicina Online');
        $response->assertSee('Huerta Familiar');
        $response->assertSee('data-ts-tab="tramites"', false);
        $response->assertSee('data-ts-tab="servicios"', false);
    }
}
