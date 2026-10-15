<?php

namespace Tests\Feature;

use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ConvertWordPressImagesTest extends TestCase
{
    use RefreshDatabase;

    private string $origen;
    private string $destinoAnio = '2099';

    protected function setUp(): void
    {
        parent::setUp();

        $this->origen = sys_get_temp_dir() . '/wp-uploads-prueba-' . uniqid();
        File::ensureDirectoryExists($this->origen . '/2099/01');

        // Foto de 2400x1200 (más ancha que el máximo de 1600) y un archivo que NO es imagen
        $img = imagecreatetruecolor(2400, 1200);
        imagefill($img, 0, 0, imagecolorallocate($img, 200, 30, 30));
        imagejpeg($img, $this->origen . '/2099/01/foto-grande.jpg');
        imagepng(imagecreatetruecolor(100, 50), $this->origen . '/2099/01/logo.PNG');
        file_put_contents($this->origen . '/2099/01/wp-config.php', '<?php // no debe tocarse');

        Noticia::create([
            'titulo'    => 'Noticia con fotos',
            'slug'      => 'noticia-con-fotos',
            'fecha'     => now(),
            'estado'    => 'publicado',
            'wp_id'     => 99001,
            'imagen_destacada' => '/images/2099/01/foto-grande_resultado.webp',
            'contenido' => '<p><img src="/images/2099/01/logo_resultado.webp" alt=""><img src="/images/2099/01/no-esta_resultado.webp" alt=""></p>',
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->origen);
        File::deleteDirectory(public_path('images/' . $this->destinoAnio));

        parent::tearDown();
    }

    public function test_dry_run_does_not_write_files(): void
    {
        $this->artisan('wordpress:convert-images', ['origen' => $this->origen, '--dry-run' => true])
            ->expectsOutputToContain('Faltan: 3')
            ->assertSuccessful();

        $this->assertFileDoesNotExist(public_path('images/2099/01/foto-grande_resultado.webp'));
    }

    public function test_it_converts_referenced_images_resizes_them_and_reports_the_missing_ones(): void
    {
        $this->artisan('wordpress:convert-images', ['origen' => $this->origen])
            ->expectsOutputToContain('/images/2099/01/no-esta_resultado.webp')
            ->assertSuccessful();

        $grande = public_path('images/2099/01/foto-grande_resultado.webp');
        $logo = public_path('images/2099/01/logo_resultado.webp');

        $this->assertFileExists($grande);
        $this->assertFileExists($logo);
        $this->assertSame(IMAGETYPE_WEBP, getimagesize($grande)[2]);
        $this->assertSame(1600, getimagesize($grande)[0], 'debe reducirse al ancho máximo');
        $this->assertSame(800, getimagesize($grande)[1], 'debe conservar la proporción');
        $this->assertSame(100, getimagesize($logo)[0], 'las imágenes chicas no se agrandan');
        $this->assertFileDoesNotExist(public_path('images/2099/01/no-esta_resultado.webp'));
    }

    public function test_it_never_converts_anything_that_is_not_referenced_or_not_an_image(): void
    {
        $this->artisan('wordpress:convert-images', ['origen' => $this->origen])->assertSuccessful();

        $this->assertSame(
            ['foto-grande_resultado.webp', 'logo_resultado.webp'],
            array_values(array_diff(scandir(public_path('images/2099/01')), ['.', '..']))
        );
    }

    public function test_it_is_idempotent(): void
    {
        $this->artisan('wordpress:convert-images', ['origen' => $this->origen])->assertSuccessful();

        $this->artisan('wordpress:convert-images', ['origen' => $this->origen])
            ->expectsOutputToContain('Faltan: 1')
            ->assertSuccessful();
    }

    public function test_it_fails_with_a_clear_message_when_the_folder_does_not_exist(): void
    {
        $this->artisan('wordpress:convert-images', ['origen' => $this->origen . '-inexistente'])
            ->expectsOutputToContain('no existe')
            ->assertFailed();
    }
}
