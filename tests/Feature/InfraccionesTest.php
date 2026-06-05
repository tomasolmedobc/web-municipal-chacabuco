<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class InfraccionesTest extends TestCase
{
    use RefreshDatabase;

    public function test_infracciones_query_page_renders(): void
    {
        $response = $this->get(route('infracciones.index'));

        $response->assertOk();
        $response->assertSee('Consulta de Infracciones');
        $response->assertSee('Datos de consulta');
        $response->assertSee('Consultar');
    }

    public function test_infracciones_button_points_to_query_page(): void
    {
        $response = $this->get(route('tramites-servicios.index'));

        $response->assertOk();
        $response->assertSee('/tramites-y-servicios/infracciones');
    }

    public function test_infracciones_query_requires_value(): void
    {
        $response = $this->post(route('infracciones.consultar'), [
            'criterio' => 'dominio',
            'valor' => '',
        ]);

        $response->assertSessionHasErrors('valor');
    }

    public function test_infracciones_page_accepts_flashed_array_results(): void
    {
        $response = $this
            ->withSession([
                'infracciones_criterio' => 'dominio',
                'infracciones_valor' => 'AF438ZP',
                'infracciones_consultado' => true,
                'infracciones_resultados' => [
                    [
                        'fecha' => '4/6/2026',
                        'patente' => 'AF438ZP',
                        'vehiculo' => 'FORD RANGER',
                        'nombre_completo' => 'DAGA BAUTISTA BENJAMIN',
                        'calle' => 'AV. ARENALES',
                        'altura' => '',
                        'causa' => '115392',
                        'acta' => '102790',
                    ],
                ],
                'infracciones_cedulas' => [],
            ])
            ->get(route('infracciones.index'));

        $response->assertOk();
        $response->assertSee('DAGA BAUTISTA BENJAMIN');
        $response->assertSee('AF438ZP');
    }

    public function test_infracciones_pdf_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('infracciones.cedula'));
        $this->assertTrue(Route::has('infracciones.libre-deuda'));
    }

    public function test_pdf_view_renders_formal_document(): void
    {
        $html = view('infracciones.pdf.libre-deuda', [
            'data' => [
                'dni' => '12345678',
                'nombre' => 'Juan',
                'apellido' => 'Perez',
            ],
            'nombreCompleto' => 'JUAN PEREZ',
            'logoPath' => public_path('images/infracciones/chacabuco.jpg'),
        ])->render();

        $this->assertStringContainsString('CONSTANCIA DE LIBRE DEUDA DE INFRACCIONES', $html);
        $this->assertStringContainsString('JUAN PEREZ', $html);
    }

    public function test_infracciones_query_works_against_local_juzgado_when_available(): void
    {
        try {
            $exists = DB::connection('juzgado')
                ->table('faltas')
                ->where('patente', 'AF438ZP')
                ->exists();
        } catch (\Throwable) {
            $this->markTestSkipped('La base local del Juzgado no esta disponible.');
        }

        if (! $exists) {
            $this->markTestSkipped('La base local del Juzgado no tiene el dominio AF438ZP.');
        }

        $response = $this->followingRedirects()->post(route('infracciones.consultar'), [
            'criterio' => 'dominio',
            'valor' => 'AF438ZP',
        ]);

        $response->assertOk();
        $response->assertSee('DAGA BAUTISTA BENJAMIN');
        $response->assertSee('AF438ZP');

        $refresh = $this->get(route('infracciones.index'));

        $refresh->assertOk();
        $refresh->assertDontSee('DAGA BAUTISTA BENJAMIN');
        $refresh->assertDontSee('value="AF438ZP"', false);
    }
}
