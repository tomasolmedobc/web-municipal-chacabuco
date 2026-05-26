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
            'logo' => config_sistema('logo'),
            'portada' => config_sistema('portada'),
            'default_noticia' => config_sistema('default_noticia'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'portada' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'default_noticia' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->boolean('eliminar_logo')) {
            $this->eliminarConfiguracionArchivo('logo');
        }

        if ($request->boolean('eliminar_portada')) {
            $this->eliminarConfiguracionArchivo('portada');
        }

        if ($request->boolean('eliminar_default_noticia')) {
            $this->eliminarConfiguracionArchivo('default_noticia');
        }

        if ($request->hasFile('logo')) {
            $this->guardarConfiguracionArchivo($request, 'logo', 'config/logo');
        }

        if ($request->hasFile('portada')) {
            $this->guardarConfiguracionArchivo($request, 'portada', 'config/portada');
        }

        if ($request->hasFile('default_noticia')) {
            $this->guardarConfiguracionArchivo($request, 'default_noticia', 'config/default-noticia');
        }

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
