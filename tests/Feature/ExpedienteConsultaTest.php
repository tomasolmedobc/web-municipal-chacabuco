<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpedienteConsultaTest extends TestCase
{
    use RefreshDatabase;

    public function test_expedientes_query_page_renders(): void
    {
        $response = $this->get(route('expedientes.index'));

        $response->assertOk();
        $response->assertSee('Consulta de Expedientes');
        $response->assertSee('Datos del expediente');
        $response->assertSee('Consultar expediente');
    }

    public function test_expedientes_button_points_to_query_page(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('/tramites-y-servicios/expedientes');
    }
}
