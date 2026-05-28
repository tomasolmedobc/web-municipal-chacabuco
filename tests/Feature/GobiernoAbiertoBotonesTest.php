<?php

namespace Tests\Feature;

use App\Models\Licitacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GobiernoAbiertoBotonesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_open_government_button_with_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($user)->post(route('admin.gobierno-abierto.store'), [
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Nomina de Empleados',
            'descripcion' => 'Listado actualizado de empleados municipales.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'orden' => 1,
            'archivos' => [
                UploadedFile::fake()->create('nomina.xlsx', 128, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ],
        ]);

        $response->assertRedirect(route('admin.gobierno-abierto.index', [
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
        ]));

        $boton = Licitacion::with('archivos')->where('titulo', 'Nomina de Empleados')->first();

        $this->assertNotNull($boton);
        $this->assertSame(Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS, $boton->categoria);
        $this->assertSame('xlsx', $boton->archivos->first()->extension);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $boton->archivos->first()->ruta));
    }

    public function test_active_open_government_buttons_are_rendered_on_public_index(): void
    {
        $boton = Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Ordenanza Vigente',
            'descripcion' => 'Ordenanza impositiva anual.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'link_externo' => 'https://example.com/ordenanza.pdf',
            'orden' => 1,
        ]);

        $response = $this->get(route('gobierno-abierto.index'));

        $response->assertOk();
        $response->assertSee($boton->titulo);
        $response->assertSee($boton->link_externo);
    }

    public function test_providers_page_is_public_and_linked_from_open_government(): void
    {
        $index = $this->get(route('gobierno-abierto.index'));

        $index->assertOk();
        $index->assertSee(route('proveedores.index'));
        $this->assertSame('http://localhost/gobierno-abierto/proveedores', route('proveedores.index'));

        $proveedores = $this->get(route('proveedores.index'));

        $proveedores->assertOk();
        $proveedores->assertSee('Compras y Proveedores');
    }

    public function test_public_index_uses_latest_active_document_for_fixed_buttons(): void
    {
        Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Ordenanza Vigente',
            'descripcion' => 'Version anterior.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'link_externo' => 'https://example.com/ordenanza-vieja.pdf',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Ordenanza Vigente',
            'descripcion' => 'Version nueva.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'link_externo' => 'https://example.com/ordenanza-nueva.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('gobierno-abierto.index'));

        $response->assertOk();
        $response->assertSee('https://example.com/ordenanza-nueva.pdf');
        $response->assertDontSee('https://example.com/ordenanza-vieja.pdf');
    }

    public function test_public_index_uses_latest_active_road_report_for_fixed_button(): void
    {
        Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Informes Viales',
            'descripcion' => 'Informe vial anterior.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'link_externo' => 'https://example.com/informe-vial-viejo.pdf',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Informes Viales',
            'descripcion' => 'Informe vial nuevo.',
            'tipo' => 'publica',
            'estado' => 'activa',
            'link_externo' => 'https://example.com/informe-vial-nuevo.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('gobierno-abierto.index'));

        $response->assertOk();
        $response->assertSee('Informes Viales');
        $response->assertSee('https://example.com/informe-vial-nuevo.pdf');
        $response->assertDontSee('https://example.com/informe-vial-viejo.pdf');
    }

    public function test_fixed_button_with_multiple_files_opens_file_picker(): void
    {
        $organigrama = Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Organigrama',
            'descripcion' => 'Estructura municipal.',
            'tipo' => 'publica',
            'estado' => 'activa',
        ]);

        $organigrama->archivos()->create([
            'nombre_original' => 'organigrama-politico.pdf',
            'nombre_archivo' => 'organigrama-politico.pdf',
            'ruta' => '/storage/gobierno-abierto/botones_archivos_links/organigrama-politico.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'tamano' => 1000,
        ]);

        $organigrama->archivos()->create([
            'nombre_original' => 'organigrama-administrativo.pdf',
            'nombre_archivo' => 'organigrama-administrativo.pdf',
            'ruta' => '/storage/gobierno-abierto/botones_archivos_links/organigrama-administrativo.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'tamano' => 1000,
        ]);

        $index = $this->get(route('gobierno-abierto.index'));

        $index->assertOk();
        $index->assertSee(route('gobierno-abierto.accesos.show', $organigrama));

        $selector = $this->get(route('gobierno-abierto.accesos.show', $organigrama));

        $selector->assertOk();
        $selector->assertSee('organigrama-politico.pdf');
        $selector->assertSee('organigrama-administrativo.pdf');
    }

    public function test_fixed_button_with_one_file_links_directly_to_file(): void
    {
        $organigrama = Licitacion::create([
            'categoria' => Licitacion::CATEGORIA_BOTONES_ARCHIVOS_LINKS,
            'titulo' => 'Organigrama',
            'descripcion' => 'Estructura municipal.',
            'tipo' => 'publica',
            'estado' => 'activa',
        ]);

        $archivo = $organigrama->archivos()->create([
            'nombre_original' => 'organigrama.pdf',
            'nombre_archivo' => 'organigrama.pdf',
            'ruta' => '/storage/gobierno-abierto/botones_archivos_links/organigrama.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'tamano' => 1000,
        ]);

        $response = $this->get(route('gobierno-abierto.index'));

        $response->assertOk();
        $response->assertSee($archivo->ruta);
        $response->assertDontSee(route('gobierno-abierto.accesos.show', $organigrama));
    }
}
