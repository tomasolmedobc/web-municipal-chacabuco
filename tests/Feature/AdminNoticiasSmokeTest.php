<?php

namespace Tests\Feature;

use App\Models\Noticia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNoticiasSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_returns_successfully_when_database_is_empty(): void
    {
        $response = $this->get('/noticias');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_news_manually(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($user)->post(route('admin.noticias.store'), [
            'titulo' => 'Nueva noticia de prueba',
            'contenido' => '<p>Contenido de prueba</p>',
            'fecha' => now()->format('Y-m-d\TH:i'),
            'estado' => 'publicado',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('noticias', [
            'titulo' => 'Nueva noticia de prueba',
            'estado' => 'publicado',
        ]);

        $this->assertSame(1, Noticia::count());
    }
}
