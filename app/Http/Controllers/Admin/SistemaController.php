<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Noticia;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SistemaController extends Controller
{
    public function index()
    {
        return view('admin.sistema.index', [
            'logo'                => config_sistema('logo'),
            'portada'             => config_sistema('portada'),
            'portada_orig'        => config_sistema('portada_orig'),
            'portada_zoom'        => config_sistema('portada_zoom', '1'),
            'portada_altura'      => config_sistema('portada_altura', '390'),
            'portada_crop_top'    => (int) config_sistema('portada_crop_top',    '0'),
            'portada_crop_bottom' => (int) config_sistema('portada_crop_bottom', '0'),
            'portada_crop_left'   => (int) config_sistema('portada_crop_left',   '0'),
            'portada_crop_right'  => (int) config_sistema('portada_crop_right',  '0'),
            'default_noticia'     => config_sistema('default_noticia'),
            'whatsapp_activo'     => config_sistema('whatsapp_activo') === '1',
            'whatsapp_url'        => config_sistema('whatsapp_url'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'portada'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'portada_zoom'        => ['nullable', 'numeric', 'min:1', 'max:2'],
            'portada_altura'      => ['nullable', 'integer', 'in:320,380,440,500'],
            'portada_crop_top'    => ['nullable', 'integer', 'min:0', 'max:45'],
            'portada_crop_bottom' => ['nullable', 'integer', 'min:0', 'max:45'],
            'portada_crop_left'   => ['nullable', 'integer', 'min:0', 'max:45'],
            'portada_crop_right'  => ['nullable', 'integer', 'min:0', 'max:45'],
            'default_noticia'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'whatsapp_url'        => ['nullable', 'url', 'max:255'],
        ]);

        if ($request->boolean('eliminar_logo')) {
            $this->eliminarConfiguracionArchivo('logo');
        }

        if ($request->boolean('eliminar_portada')) {
            $this->eliminarConfiguracionArchivo('portada');
            $this->eliminarConfiguracionArchivo('portada_orig');
        }

        if ($request->boolean('eliminar_default_noticia')) {
            $this->eliminarConfiguracionArchivo('default_noticia');
        }

        if ($request->hasFile('logo')) {
            $this->guardarConfiguracionArchivo($request, 'logo', 'config/logo');
        }

        if ($request->hasFile('portada')) {
            $this->guardarConfiguracionArchivo($request, 'portada', 'config/portada');
            // Guardar copia del original para re-recortar sin degradar
            $this->guardarPortadaOriginal();
            // Nueva imagen: resetear crop
            foreach (['portada_crop_top','portada_crop_bottom','portada_crop_left','portada_crop_right'] as $k) {
                Configuracion::updateOrCreate(['clave' => $k], ['valor' => '0']);
                config_sistema_flush($k);
            }
        }

        if ($request->hasFile('default_noticia')) {
            $this->guardarConfiguracionArchivo($request, 'default_noticia', 'config/default-noticia');
        }

        // Ajustes de portada — zoom y altura
        $portadaZoom = number_format(max(1, min(2, (float) $request->input('portada_zoom', 1))), 2);
        Configuracion::updateOrCreate(['clave' => 'portada_zoom'], ['valor' => $portadaZoom]);
        config_sistema_flush('portada_zoom');

        $portadaAltura = $request->input('portada_altura', '390');
        Configuracion::updateOrCreate(['clave' => 'portada_altura'], ['valor' => $portadaAltura]);
        config_sistema_flush('portada_altura');

        // Recorte de portada
        $cropTop    = max(0, min(45, (int) $request->input('portada_crop_top',    0)));
        $cropBottom = max(0, min(45, (int) $request->input('portada_crop_bottom', 0)));
        $cropLeft   = max(0, min(45, (int) $request->input('portada_crop_left',   0)));
        $cropRight  = max(0, min(45, (int) $request->input('portada_crop_right',  0)));

        foreach (['portada_crop_top' => $cropTop, 'portada_crop_bottom' => $cropBottom,
                  'portada_crop_left' => $cropLeft, 'portada_crop_right' => $cropRight] as $k => $v) {
            Configuracion::updateOrCreate(['clave' => $k], ['valor' => (string) $v]);
            config_sistema_flush($k);
        }

        // Aplicar recorte con GD si hay portada disponible
        if (Configuracion::where('clave', 'portada')->exists()) {
            $this->aplicarCropGD($cropTop, $cropBottom, $cropLeft, $cropRight);
        }

        // WhatsApp
        $whatsappActivo = $request->boolean('whatsapp_activo') ? '1' : '0';
        Configuracion::updateOrCreate(['clave' => 'whatsapp_activo'], ['valor' => $whatsappActivo]);
        config_sistema_flush('whatsapp_activo');

        $whatsappUrl = trim($request->input('whatsapp_url', ''));
        Configuracion::updateOrCreate(['clave' => 'whatsapp_url'], ['valor' => $whatsappUrl]);
        config_sistema_flush('whatsapp_url');

        return back()->with('ok', 'Configuración actualizada correctamente');
    }

    private function guardarConfiguracionArchivo(Request $request, string $clave, string $carpeta): void
    {
        $this->eliminarArchivoAnterior($clave);

        $ruta = $this->procesarImagenWebp($request->file($clave), $carpeta, $clave);

        Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $ruta]
        );

        config_sistema_flush($clave);
    }

    private function procesarImagenWebp(UploadedFile $archivo, string $carpeta, string $clave): string
    {
        $directorio = Storage::disk('public')->path(trim($carpeta, '/'));
        File::ensureDirectoryExists($directorio);

        $nombreBase = $this->nombreBaseUnico($directorio, $this->nombreBaseFecha(
            pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME)
        ), 'webp');

        $rutaWebp = $directorio . '/' . $nombreBase . '.webp';
        $extension = strtolower($archivo->getClientOriginalExtension());

        if ($extension === 'webp') {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $archivo->getRealPath());
            finfo_close($finfo);

            if ($realMime !== 'image/webp') {
                throw ValidationException::withMessages([
                    $clave => 'El archivo no es un WebP válido.',
                ]);
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
            throw ValidationException::withMessages([
                $clave => 'No se pudo procesar la imagen.',
            ]);
        }

        if ($extension === 'png') {
            imagepalettetotruecolor($imagen);
            imagealphablending($imagen, true);
            imagesavealpha($imagen, true);
        }

        $maxAncho = 1600;
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

        imagewebp($imagen, $rutaWebp, 85);
        imagedestroy($imagen);

        return '/storage/' . trim($carpeta, '/') . '/' . $nombreBase . '.webp';
    }

    private function nombreBaseFecha(string $nombreOriginal): string
    {
        return now()->format('dmY_Hi') . '_' . Str::slug($nombreOriginal);
    }

    private function nombreBaseUnico(string $directorio, string $nombreBase, string $extension): string
    {
        $nombreDisponible = $nombreBase;
        $contador = 2;

        while (File::exists($directorio . '/' . $nombreDisponible . '.' . $extension)) {
            $nombreDisponible = $nombreBase . '_' . $contador;
            $contador++;
        }

        return $nombreDisponible;
    }

    private function eliminarConfiguracionArchivo(string $clave): void
    {
        $this->eliminarArchivoAnterior($clave);

        Configuracion::where('clave', $clave)->delete();

        config_sistema_flush($clave);
    }

    private function eliminarArchivoAnterior(string $clave): void
    {
        $valorActual = Configuracion::where('clave', $clave)->value('valor');

        if (!$valorActual && $clave === 'default_noticia') {
            $this->liberarNoticiasConImagenDefault('');
            return;
        }

        if (!$valorActual) {
            return;
        }

        if ($clave === 'default_noticia') {
            $this->liberarNoticiasConImagenDefault($valorActual);
        }

        $rutaRelativa = str_replace('/storage/', '', $valorActual);

        if (Storage::disk('public')->exists($rutaRelativa)) {
            Storage::disk('public')->delete($rutaRelativa);
        }
    }

    private function guardarPortadaOriginal(): void
    {
        $portadaPath = Configuracion::where('clave', 'portada')->value('valor');
        if (!$portadaPath) return;

        $relPortada = str_replace('/storage/', '', $portadaPath);
        if (!Storage::disk('public')->exists($relPortada)) return;

        $dirOrig = Storage::disk('public')->path('config/portada-orig');
        File::ensureDirectoryExists($dirOrig);

        $nombre    = basename($relPortada);
        $destAbs   = $dirOrig . '/' . $nombre;
        $srcAbs    = Storage::disk('public')->path($relPortada);

        copy($srcAbs, $destAbs);

        $origPath = '/storage/config/portada-orig/' . $nombre;
        Configuracion::updateOrCreate(['clave' => 'portada_orig'], ['valor' => $origPath]);
        config_sistema_flush('portada_orig');
    }

    private function aplicarCropGD(int $top, int $bottom, int $left, int $right): void
    {
        // Fuente: original si existe, sino la portada actual
        $origPath   = Configuracion::where('clave', 'portada_orig')->value('valor');
        $portadaPath = Configuracion::where('clave', 'portada')->value('valor');
        $source     = $origPath ?? $portadaPath;

        if (!$source) return;

        $relSource = str_replace('/storage/', '', $source);
        if (!Storage::disk('public')->exists($relSource)) return;

        $srcAbs = Storage::disk('public')->path($relSource);
        $img    = imagecreatefromwebp($srcAbs);
        if (!$img) {
            \Illuminate\Support\Facades\Log::warning("aplicarCropGD: no se pudo abrir {$srcAbs}");
            return;
        }

        $srcW = imagesx($img);
        $srcH = imagesy($img);

        $x = (int) round($srcW * $left   / 100);
        $y = (int) round($srcH * $top    / 100);
        $w = (int) round($srcW * (100 - $left - $right)  / 100);
        $h = (int) round($srcH * (100 - $top  - $bottom) / 100);

        $w = max(10, $w);
        $h = max(10, $h);

        // Sin recorte: restaurar original
        if ($w === $srcW && $h === $srcH) {
            imagedestroy($img);
            if ($origPath && $origPath !== $portadaPath) {
                $relDest = str_replace('/storage/', '', $portadaPath);
                copy($srcAbs, Storage::disk('public')->path($relDest));
                config_sistema_flush('portada');
            }
            return;
        }

        $cropped = imagecrop($img, ['x' => $x, 'y' => $y, 'width' => $w, 'height' => $h]);
        imagedestroy($img);
        if (!$cropped) return;

        $relDest = str_replace('/storage/', '', $portadaPath);
        $destAbs = Storage::disk('public')->path($relDest);
        imagewebp($cropped, $destAbs, 85);
        imagedestroy($cropped);

        config_sistema_flush('portada');
    }

    private function liberarNoticiasConImagenDefault(string $valorActual): void
    {
        Noticia::where(function ($query) use ($valorActual) {
            $query->where('imagen_destacada', $valorActual)
                ->orWhere('imagen_destacada', '/images/importantes/default-noticia.webp')
                ->orWhere('imagen_destacada', 'like', '/storage/config/default-noticia/%');
        })->update([
            'imagen_destacada' => null,
        ]);
    }
}
