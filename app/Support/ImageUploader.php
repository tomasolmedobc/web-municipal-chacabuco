<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
    private const EXTENSIONES_SEGURAS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip',
        'jpg', 'jpeg', 'png', 'webp', 'gif',
    ];

    /**
     * Extensión para guardar un archivo subido. Conserva la que eligió el usuario
     * solo si está en la lista segura; si no, usa la detectada por contenido, y
     * como último recurso 'bin'. Evita guardar .php u otros ejecutables en carpetas públicas.
     */
    public static function extensionSegura(UploadedFile $archivo): string
    {
        $cliente = strtolower($archivo->getClientOriginalExtension());

        if (in_array($cliente, self::EXTENSIONES_SEGURAS, true)) {
            return $cliente;
        }

        $detectada = strtolower((string) $archivo->guessExtension());

        return in_array($detectada, self::EXTENSIONES_SEGURAS, true) ? $detectada : 'bin';
    }

    public static function guardarWebp(UploadedFile $archivo, string $carpeta, int $maxAncho = 1600, int $calidad = 85): ?string
    {
        $directorio = Storage::disk('public')->path(trim($carpeta, '/'));
        File::ensureDirectoryExists($directorio);

        $nombreBase = self::nombreBaseUnico($directorio, self::nombreBaseFecha(
            pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)
        ), 'webp');

        $rutaWebp = $directorio . '/' . $nombreBase . '.webp';
        $extension = strtolower($archivo->getClientOriginalExtension());

        if ($extension === 'webp') {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $archivo->getRealPath());
            finfo_close($finfo);

            if ($realMime !== 'image/webp') {
                return null;
            }

            $archivo->move($directorio, $nombreBase . '.webp');

            return '/storage/' . trim($carpeta, '/') . '/' . $nombreBase . '.webp';
        }

        $imagen = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($archivo->getRealPath()),
            'png' => imagecreatefrompng($archivo->getRealPath()),
            default => null,
        };

        if (! $imagen) {
            return null;
        }

        if ($extension === 'png') {
            imagepalettetotruecolor($imagen);
            imagealphablending($imagen, true);
            imagesavealpha($imagen, true);
        }

        $anchoOriginal = imagesx($imagen);
        $altoOriginal = imagesy($imagen);

        if ($anchoOriginal > $maxAncho) {
            $nuevoAncho = $maxAncho;
            $nuevoAlto = (int) round(($altoOriginal / $anchoOriginal) * $nuevoAncho);
            $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

            imagealphablending($imagenRedimensionada, false);
            imagesavealpha($imagenRedimensionada, true);

            $transparente = imagecolorallocatealpha($imagenRedimensionada, 0, 0, 0, 127);
            imagefill($imagenRedimensionada, 0, 0, $transparente);

            imagecopyresampled(
                $imagenRedimensionada,
                $imagen,
                0,
                0,
                0,
                0,
                $nuevoAncho,
                $nuevoAlto,
                $anchoOriginal,
                $altoOriginal
            );

            imagedestroy($imagen);
            $imagen = $imagenRedimensionada;
        }

        imagewebp($imagen, $rutaWebp, $calidad);
        imagedestroy($imagen);

        return '/storage/' . trim($carpeta, '/') . '/' . $nombreBase . '.webp';
    }

    /**
     * Guarda la imagen como webp en public/images/{carpeta}/{Y}/{m}/ y conserva
     * el original en uploads_originales/{carpeta}/{Y}/{m}/. Retorna la ruta pública
     * relativa (/images/...) o null si el formato no es soportado.
     */
    public static function guardarWebpConOriginal(
        UploadedFile $archivo,
        string $carpeta,
        int $maxAncho = 1600,
        int $calidad = 85
    ): ?string {
        $anio = now()->format('Y');
        $mes  = now()->format('m');

        // Originales fuera de public/ — no accesibles por URL directa
        $directorioOriginal = storage_path("app/private/uploads_originales/{$carpeta}/{$anio}/{$mes}");
        $directorioWebp     = public_path("images/{$carpeta}/{$anio}/{$mes}");

        File::ensureDirectoryExists($directorioOriginal);
        File::ensureDirectoryExists($directorioWebp);

        $extension  = strtolower($archivo->getClientOriginalExtension());
        $nombreBase = self::nombreBaseUnico($directorioWebp, self::nombreBaseFecha(
            pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)
        ), 'webp');

        $rutaOriginal = $directorioOriginal . '/' . $nombreBase . '.' . $extension;
        $rutaWebp     = $directorioWebp . '/' . $nombreBase . '.webp';

        $archivo->move($directorioOriginal, $nombreBase . '.' . $extension);

        if ($extension === 'webp') {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $rutaOriginal);
            finfo_close($finfo);

            if ($realMime !== 'image/webp') {
                @unlink($rutaOriginal);
                return null;
            }

            copy($rutaOriginal, $rutaWebp);
            return "/images/{$carpeta}/{$anio}/{$mes}/{$nombreBase}.webp";
        }

        $imagen = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($rutaOriginal),
            'png'         => imagecreatefrompng($rutaOriginal),
            default       => null,
        };

        if (! $imagen) {
            return null;
        }

        if ($extension === 'png') {
            imagepalettetotruecolor($imagen);
            imagealphablending($imagen, true);
            imagesavealpha($imagen, true);
        }

        $anchoOriginal = imagesx($imagen);
        $altoOriginal  = imagesy($imagen);

        if ($anchoOriginal > $maxAncho) {
            $nuevoAncho           = $maxAncho;
            $nuevoAlto            = (int) round(($altoOriginal / $anchoOriginal) * $nuevoAncho);
            $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

            imagealphablending($imagenRedimensionada, false);
            imagesavealpha($imagenRedimensionada, true);
            $transparente = imagecolorallocatealpha($imagenRedimensionada, 0, 0, 0, 127);
            imagefill($imagenRedimensionada, 0, 0, $transparente);
            imagecopyresampled($imagenRedimensionada, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

            imagedestroy($imagen);
            $imagen = $imagenRedimensionada;
        }

        imagewebp($imagen, $rutaWebp, $calidad);
        imagedestroy($imagen);

        return "/images/{$carpeta}/{$anio}/{$mes}/{$nombreBase}.webp";
    }

    public static function eliminar(?string $ruta): void
    {
        if (! $ruta || ! str_starts_with($ruta, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $ruta));
    }

    public static function nombreBaseFecha(string $nombreOriginal): string
    {
        return now()->format('dmY_Hi') . '_' . Str::slug($nombreOriginal);
    }

    public static function nombreBaseUnico(string $directorio, string $nombreBase, string $extension): string
    {
        $nombreDisponible = $nombreBase;
        $contador = 2;

        while (File::exists($directorio . '/' . $nombreDisponible . '.' . $extension)) {
            $nombreDisponible = $nombreBase . '_' . $contador;
            $contador++;
        }

        return $nombreDisponible;
    }
}
