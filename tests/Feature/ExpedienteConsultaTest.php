<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_expedientes_page_accepts_flashed_array_results(): void
    {
        $response = $this
            ->withSession([
                'expedientes_form' => [
                    'numero' => 776,
                    'letra' => '',
                    'anio' => 2026,
                ],
                'expedientes_resultado' => [
                    'IdExpediente' => 60337,
                    'IdTramite' => 1,
                    'Descripcion' => 'CERTIFICACION DE DEUDA',
                    'Detalle' => 'Consulta de prueba',
                    'Nombres' => null,
                    'Apellidos' => null,
                    'RazonSocial' => null,
                    'Estado' => 1,
                    'FechaHoraIngreso' => '2026-01-01 10:00:00',
                    'CodExpediente' => null,
                    'ObservacionPub' => null,
                    'MotivoAnulacion' => null,
                    'Anio' => 2026,
                    'Letra' => null,
                    'NumExpediente' => 776,
                ],
                'expedientes_pasos' => [],
            ])
            ->get(route('expedientes.index'));

        $response->assertOk();
        $response->assertSee('CERTIFICACION DE DEUDA');
        $response->assertSee('776');
    }

    public function test_expedientes_query_works_against_local_database_when_available(): void
    {
        try {
            $exists = DB::connection('expedientes')
                ->table('expedientes')
                ->where('NumExpediente', 776)
                ->where('Anio', 2026)
                ->exists();
        } catch (\Throwable) {
            $this->markTestSkipped('La base local de expedientes no esta disponible.');
        }

        if (! $exists) {
            $this->markTestSkipped('La base local de expedientes no tiene el expediente 776/2026.');
        }

        $response = $this->followingRedirects()->post(route('expedientes.consultar'), [
            'numero' => 776,
            'letra' => '',
            'anio' => 2026,
        ]);

        $response->assertOk();
        $response->assertSee('CERTIFICACION DE DEUDA');
        $response->assertSee('776');

        $refresh = $this->get(route('expedientes.index'));

        $refresh->assertOk();
        $refresh->assertDontSee('CERTIFICACION DE DEUDA');
        $refresh->assertDontSee('value="776"', false);
    }
}
