<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoMunicipalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'acceso_municipal.password' => 'clave-test',
            'acceso_municipal.links' => [
                [
                    'titulo' => 'Acceso 1',
                    'descripcion' => 'Descripcion 1',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/1',
                ],
                [
                    'titulo' => 'Acceso 2',
                    'descripcion' => 'Descripcion 2',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/2',
                ],
                [
                    'titulo' => 'Acceso 3',
                    'descripcion' => 'Descripcion 3',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/3',
                ],
                [
                    'titulo' => 'Acceso 4',
                    'descripcion' => 'Descripcion 4',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/4',
                ],
                [
                    'titulo' => 'Acceso 5',
                    'descripcion' => 'Descripcion 5',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/5',
                ],
                [
                    'titulo' => 'Acceso 6',
                    'descripcion' => 'Descripcion 6',
                    'icono' => 'fa-link',
                    'url' => 'https://example.com/6',
                ],
            ],
        ]);
    }

    public function test_access_municipal_requires_password(): void
    {
        $response = $this->get(route('acceso-municipal.index'));

        $response->assertOk();
        $response->assertSee('Clave de acceso');
        $response->assertDontSee('Acceso 1');
    }

    public function test_access_municipal_rejects_invalid_password(): void
    {
        $response = $this->post(route('acceso-municipal.authenticate'), [
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('password');

        $this->get(route('acceso-municipal.index'))
            ->assertSee('Clave de acceso')
            ->assertDontSee('Acceso 1');
    }

    public function test_access_municipal_allows_valid_password_and_renders_links(): void
    {
        $response = $this->post(route('acceso-municipal.authenticate'), [
            'password' => 'clave-test',
        ]);

        $response->assertRedirect(route('acceso-municipal.index'));

        $page = $this->get(route('acceso-municipal.index'));

        $page->assertOk();
        $page->assertSee('Acceso 1');
        $page->assertSee('https://example.com/6');
        $page->assertDontSee('Clave de acceso');
    }
}
