<?php

namespace Tests\Feature;

use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SistemaConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_images_are_converted_to_webp(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->forceFill(['rol' => 'admin'])->save();

        $response = $this->actingAs($user)->put(route('admin.sistema.update'), [
            'portada' => UploadedFile::fake()->image('portada.png', 800, 400),
        ]);

        $response->assertRedirect();

        $valor = Configuracion::where('clave', 'portada')->value('valor');

        $this->assertNotNull($valor);
        $this->assertStringStartsWith('/storage/config/portada/', $valor);
        $this->assertStringEndsWith('.webp', $valor);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $valor));
    }
}
