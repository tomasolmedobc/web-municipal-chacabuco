<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ConvertWordPressImages extends Command
{
    protected $signature = 'wordpress:convert-images
        {origen : Carpeta con las fotos de WordPress: la carpeta uploads (con subcarpetas 2026/08...) o la del año (con 08, 09...)}
        {--dry-run : Muestra qué convertiría sin escribir archivos}
        {--calidad=85 : Calidad WebP (1-100)}
        {--ancho=1600 : Ancho máximo en píxeles (las más chicas no se agrandan)}
        {--desde-wp-id=0 : Solo considera noticias con wp_id mayor a este valor}
        {--lista-faltantes= : Guarda en este archivo de texto la lista completa de las imágenes cuyo archivo de origen no se encontró}';

    protected $description = 'Convierte a WebP (nombre_resultado.webp) las imágenes que usan las noticias importadas de WordPress y todavía no existen en public/images';

    private const EXTENSIONES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** @var array<string, array<string, string>> listado de cada carpeta de origen (nombre en minúsculas => nombre real) */
    private array $listados = [];

    public function handle(): int
    {
        $origen = realpath((string) $this->argument('origen'));

        if ($origen === false || ! is_dir($origen)) {
            $this->error('La carpeta de origen no existe: ' . $this->argument('origen'));

            return self::FAILURE;
        }

        if (! function_exists('imagewebp')) {
            $this->error('PHP no tiene soporte WebP en GD (imagewebp).');

            return self::FAILURE;
        }

        @ini_set('memory_limit', '768M');

        $dryRun = (bool) $this->option('dry-run');
        $calidad = max(1, min(100, (int) $this->option('calidad')));
        $anchoMax = max(100, (int) $this->option('ancho'));

        $referencias = $this->imagenesReferenciadas((int) $this->option('desde-wp-id'));
        $this->info('Imágenes distintas referenciadas por las noticias: ' . count($referencias));

        $faltantes = array_filter($referencias, fn (array $r) => ! file_exists($this->destino($r)));
        $this->line('Ya existen en public/images: ' . (count($referencias) - count($faltantes)) . ' | Faltan: ' . count($faltantes));

        if ($dryRun) {
            $this->comment('Modo simulación activado: no se escribirá ningún archivo.');
        }

        $convertidas = 0;
        $sinOrigen = [];
        $conError = [];

        foreach ($faltantes as $r) {
            $fuente = $this->buscarOrigen($origen, $r);

            if ($fuente === null) {
                $sinOrigen[] = $r;
                continue;
            }

            if ($dryRun) {
                $convertidas++;
                continue;
            }

            if ($this->convertir($fuente, $this->destino($r), $calidad, $anchoMax)) {
                $convertidas++;
            } else {
                $conError[] = $r;
            }
        }

        $this->newLine();
        $this->table(
            ['Faltaban', $dryRun ? 'Se convertirían' : 'Convertidas', 'Sin archivo de origen', 'Con error'],
            [[count($faltantes), $convertidas, count($sinOrigen), count($conError)]]
        );

        $this->listar('No se encontró el archivo de origen para', $sinOrigen);
        $this->listar('No se pudo convertir', $conError);

        if ($this->option('lista-faltantes') && $sinOrigen !== []) {
            // Nombre del archivo tal como está en WordPress (sin el sufijo _resultado.webp), para ubicarlo en wp-content/uploads
            $lineas = array_map(fn (array $r) => "{$r['anio']}/{$r['mes']}/{$r['nombre']}", $sinOrigen);
            File::put((string) $this->option('lista-faltantes'), implode(PHP_EOL, $lineas) . PHP_EOL);
            $this->info('Lista completa guardada en: ' . $this->option('lista-faltantes'));
        }

        return self::SUCCESS;
    }

    /**
     * Rutas /images/AAAA/MM/nombre_resultado.webp que aparecen en la imagen destacada o en el texto de las noticias.
     *
     * @return array<string, array{anio: string, mes: string, nombre: string}>
     */
    private function imagenesReferenciadas(int $desdeWpId): array
    {
        $patron = '#/images/(\d{4})/(\d{2})/([^"\'\s)?<>/\\\\]+)_resultado\.webp#i';
        $refs = [];

        DB::table('noticias')
            ->where('wp_id', '>', $desdeWpId)
            ->select(['id', 'imagen_destacada', 'contenido'])
            ->orderBy('id')
            ->chunk(300, function ($noticias) use (&$refs, $patron) {
                foreach ($noticias as $n) {
                    preg_match_all($patron, (string) $n->imagen_destacada . ' ' . (string) $n->contenido, $m, PREG_SET_ORDER);

                    foreach ($m as $x) {
                        // Los nombres vienen del texto de las noticias: se descartan los que intenten salir de la carpeta
                        if (str_contains($x[3], '..')) {
                            continue;
                        }

                        $refs["{$x[1]}/{$x[2]}/{$x[3]}"] = ['anio' => $x[1], 'mes' => $x[2], 'nombre' => $x[3]];
                    }
                }
            });

        ksort($refs);

        return $refs;
    }

    private function destino(array $r): string
    {
        return public_path("images/{$r['anio']}/{$r['mes']}/{$r['nombre']}_resultado.webp");
    }

    /** Busca nombre.jpg / .jpeg / .png / .gif / .webp en origen/AAAA/MM o en origen/MM. */
    private function buscarOrigen(string $origen, array $r): ?string
    {
        foreach (["{$origen}/{$r['anio']}/{$r['mes']}", "{$origen}/{$r['mes']}"] as $carpeta) {
            $listado = $this->listado($carpeta);

            foreach (self::EXTENSIONES as $ext) {
                $real = $listado[strtolower($r['nombre'] . '.' . $ext)] ?? null;

                if ($real === null) {
                    continue;
                }

                $ruta = realpath($carpeta . '/' . $real);

                // Debe seguir estando dentro de la carpeta de origen
                if ($ruta !== false && str_starts_with($ruta, $origen)) {
                    return $ruta;
                }
            }
        }

        return null;
    }

    /** @return array<string, string> */
    private function listado(string $carpeta): array
    {
        if (! isset($this->listados[$carpeta])) {
            $this->listados[$carpeta] = [];

            if (is_dir($carpeta)) {
                foreach (scandir($carpeta) ?: [] as $archivo) {
                    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                    if (in_array($ext, self::EXTENSIONES, true)) {
                        $this->listados[$carpeta][strtolower($archivo)] = $archivo;
                    }
                }
            }
        }

        return $this->listados[$carpeta];
    }

    private function convertir(string $fuente, string $destino, int $calidad, int $anchoMax): bool
    {
        $ext = strtolower(pathinfo($fuente, PATHINFO_EXTENSION));

        $imagen = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($fuente),
            'png'         => @imagecreatefrompng($fuente),
            'gif'         => @imagecreatefromgif($fuente),
            'webp'        => @imagecreatefromwebp($fuente),
            default       => false,
        };

        if (! $imagen) {
            return false;
        }

        // Respeta la orientación de las fotos tomadas con el celular
        if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
            $exif = @exif_read_data($fuente);
            $giro = match ((int) ($exif['Orientation'] ?? 1)) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };

            if ($giro !== 0 && ($rotada = imagerotate($imagen, $giro, 0))) {
                $imagen = $rotada;
            }
        }

        imagepalettetotruecolor($imagen);
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true);

        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);

        if ($ancho > $anchoMax) {
            $nuevoAlto = (int) round($alto * $anchoMax / $ancho);
            $escalada = imagescale($imagen, $anchoMax, $nuevoAlto);

            if ($escalada) {
                $imagen = $escalada;
                imagealphablending($imagen, false);
                imagesavealpha($imagen, true);
            }
        }

        File::ensureDirectoryExists(dirname($destino));
        $ok = imagewebp($imagen, $destino, $calidad);

        return $ok && is_file($destino) && filesize($destino) > 0;
    }

    private function listar(string $titulo, array $items): void
    {
        if ($items === []) {
            return;
        }

        $this->warn($titulo . ' ' . count($items) . ' imagen(es):');

        foreach (array_slice($items, 0, 15) as $r) {
            $this->line("  /images/{$r['anio']}/{$r['mes']}/{$r['nombre']}_resultado.webp");
        }

        if (count($items) > 15) {
            $this->line('  ... y ' . (count($items) - 15) . ' más');
        }
    }
}
