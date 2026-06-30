<?php

namespace Tests\Feature;

use App\Models\TelefonoUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelefonosUtilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_renders_correctly(): void
    {
        TelefonoUtil::create([
            'nombre'    => 'Municipalidad de Chacabuco',
            'categoria' => 'Gobierno Municipal',
            'telefono'  => '02352-123456',
            'estado'    => 'visible',
        ]);

        $response = $this->get(route('telefonos-utiles.index'));

        $response->assertOk();
        $response->assertSee('Municipalidad de Chacabuco');
        $response->assertSee('02352-123456');
        $response->assertSee('Gobierno Municipal');
    }

    public function test_hidden_entries_do_not_appear_on_public_page(): void
    {
        TelefonoUtil::create([
            'nombre'   => 'Entrada oculta',
            'categoria' => 'Salud',
            'estado'   => 'oculto',
        ]);

        $response = $this->get(route('telefonos-utiles.index'));

        $response->assertOk();
        $response->assertDontSee('Entrada oculta');
    }

    public function test_search_filters_by_name(): void
    {
        TelefonoUtil::create(['nombre' => 'Hospital Municipal', 'categoria' => 'Salud', 'estado' => 'visible']);
        TelefonoUtil::create(['nombre' => 'Comisaria Primera',  'categoria' => 'Seguridad', 'estado' => 'visible']);

        $response = $this->get(route('telefonos-utiles.index', ['q' => 'hospital']));

        $response->assertOk();
        $response->assertSee('Hospital Municipal');
        $response->assertDontSee('Comisaria Primera');
    }

    public function test_category_filter_shows_only_matching_entries(): void
    {
        TelefonoUtil::create(['nombre' => 'Hospital Municipal', 'categoria' => 'Salud',     'estado' => 'visible']);
        TelefonoUtil::create(['nombre' => 'Comisaria Primera',  'categoria' => 'Seguridad', 'estado' => 'visible']);

        $response = $this->get(route('telefonos-utiles.index', ['categoria' => 'Salud']));

        $response->assertOk();
        $response->assertSee('Hospital Municipal');
        $response->assertDontSee('Comisaria Primera');
    }

    public function test_page_renders_when_no_entries_exist(): void
    {
        $response = $this->get(route('telefonos-utiles.index'));

        $response->assertOk();
    }

    public function test_header_links_to_telefonos_utiles(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('telefonos-utiles.index'), false);
    }
}
