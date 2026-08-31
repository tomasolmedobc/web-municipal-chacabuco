<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PopupAdminController extends Controller
{
    public function index()
    {
        return view('admin.popup.index', [
            'popup_activo'      => config_sistema('popup_activo', '0') === '1',
            'popup_imagen'      => config_sistema('popup_imagen'),
            'popup_boton_texto' => config_sistema('popup_boton_texto'),
            'popup_boton_url'   => config_sistema('popup_boton_url'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'popup_imagen'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'popup_boton_texto' => ['nullable', 'string', 'max:60'],
            'popup_boton_url'   => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/)/'],
        ], [
            'popup_boton_url.regex' => 'Debe ser una URL completa (https://...) o una ruta interna (/pagina).',
        ]);

        $this->guardarClave('popup_activo', $request->boolean('popup_activo') ? '1' : '0');

        $this->guardarClave('popup_boton_texto', trim($request->input('popup_boton_texto', '')));
        $this->guardarClave('popup_boton_url',   trim($request->input('popup_boton_url',   '')));

        if ($request->boolean('eliminar_popup_imagen')) {
            $this->eliminarImagen();
        }

        if ($request->hasFile('popup_imagen')) {
            $this->guardarImagen($request->file('popup_imagen'));
        }

        AuditLog::registrar('editar', 'Popup', null, 'Popup de anuncio actualizado');

        return back()->with('ok', 'Popup actualizado correctamente');
    }

    private function guardarClave(string $clave, string $valor): void
    {
        if ($valor !== '') {
            Configuracion::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        } else {
            Configuracion::where('clave', $clave)->delete();
        }
        config_sistema_flush($clave);
    }

    private function guardarImagen(UploadedFile $archivo): void
    {
        $this->eliminarImagen();

        Storage::disk('public')->makeDirectory('config/popup');
        $directorio = Storage::disk('public')->path('config/popup');

        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombre    = 'popup_' . now()->format('dmY_Hi');
        $rutaWebp  = $directorio . '/' . $nombre . '.webp';

        if ($extension === 'webp') {
            $archivo->move($directorio, $nombre . '.webp');
        } else {
            $imagen = match ($extension) {
                'jpg', 'jpeg' => imagecreatefromjpeg($archivo->getRealPath()),
                'png'         => imagecreatefrompng($archivo->getRealPath()),
                default       => null,
            };

            if (! $imagen) {
                throw ValidationException::withMessages(['popup_imagen' => 'No se pudo procesar la imagen.']);
            }

            if ($extension === 'png') {
                imagepalettetotruecolor($imagen);
                imagealphablending($imagen, true);
                imagesavealpha($imagen, true);
            }

            $maxAncho       = 1200;
            $anchoOriginal  = imagesx($imagen);
            $altoOriginal   = imagesy($imagen);

            if ($anchoOriginal > $maxAncho) {
                $nuevoAncho = $maxAncho;
                $nuevoAlto  = (int) round(($altoOriginal / $anchoOriginal) * $nuevoAncho);
                $imagenRedim = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                imagecopyresampled($imagenRedim, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);
                $imagen = $imagenRedim;
            }

            imagewebp($imagen, $rutaWebp, 85);
        }

        $ruta = '/storage/config/popup/' . $nombre . '.webp';
        Configuracion::updateOrCreate(['clave' => 'popup_imagen'], ['valor' => $ruta]);
        config_sistema_flush('popup_imagen');
    }

    private function eliminarImagen(): void
    {
        $valorActual = Configuracion::where('clave', 'popup_imagen')->value('valor');
        if (! $valorActual) {
            return;
        }

        $rutaRelativa = ltrim(str_replace('/storage/', '', $valorActual), '/');
        if (Storage::disk('public')->exists($rutaRelativa)) {
            Storage::disk('public')->delete($rutaRelativa);
        }

        Configuracion::where('clave', 'popup_imagen')->delete();
        config_sistema_flush('popup_imagen');
    }
}
