<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploader
{
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

    public static function eliminar(?string $ruta): void
    {
        if (! $ruta || ! str_starts_with($ruta, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $ruta));
    }

    private static function nombreBaseFecha(string $nombreOriginal): string
    {
        return now()->format('dmY_Hi') . '_' . Str::slug($nombreOriginal);
    }

    private static function nombreBaseUnico(string $directorio, string $nombreBase, string $extension): string
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
