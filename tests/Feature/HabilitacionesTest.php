<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabilitacionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_habilitaciones_page_renders_content_and_documents(): void
    {
        $response = $this->get(route('habilitaciones.index'));

        $response->assertOk();
        $response->assertSee('Habilitaciones');
        $response->assertSee('Tramites Online');
        $response->assertSee('Tramites presenciales obligatorios');
        $response->assertSee('Tramite de Prefactibilidad');
        $response->assertSee('Solicitud de Pre Factibilidad');
        $response->assertSee('Requisitos de Defensa Civil');
        $response->assertSee('Listado de ordenanzas de referencia');
    }

    public function test_habilitaciones_button_points_to_public_page(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('/tramites-y-servicios/habilitaciones');
    }
}
