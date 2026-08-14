<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CarnetConfiguracion;
use App\Models\CarnetMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CarnetAdminController extends Controller
{
    private const PDF_DIR = 'files/carnet';

    public function index()
    {
        $config     = CarnetConfiguracion::instancia();
        $materiales = CarnetMaterial::orderBy('orden')->orderBy('id')->get();

        return view('admin.carnet.index', compact('config', 'materiales'));
    }

    public function editConfig()
    {
        return view('admin.carnet.config', [
            'config' => CarnetConfiguracion::instancia(),
        ]);
    }

    public function updateConfig(Request $request)
    {
        $data = $request->validate([
            'intro_texto'               => 'nullable|string',
            'alerta_info'               => 'nullable|string|max:500',
            'aviso_ubicacion'           => 'nullable|string|max:500',
            'paso1_titulo'              => 'required|string|max:255',
            'paso1_contenido'           => 'nullable|string',
            'paso2_titulo'              => 'required|string|max:255',
            'paso2_contenido'           => 'nullable|string',
            'paso3_titulo'              => 'required|string|max:255',
            'paso3_contenido'           => 'nullable|string',
            'paso4_titulo'              => 'required|string|max:255',
            'paso4_contenido'           => 'nullable|string',
            'licencia_digital_contenido' => 'nullable|string',
        ]);

        $config = CarnetConfiguracion::instancia();
        $config->update($data);

        AuditLog::registrar('editar', 'CarnetConfiguracion', $config->id, 'Contenido de Carnet de Conducir actualizado.');

        return redirect()->route('admin.carnet.index')
            ->with('ok', 'Contenido actualizado correctamente.');
    }

    public function createMaterial()
    {
        $item = new CarnetMaterial(['activo' => true, 'tipo_boton' => 'descargar', 'orden' => 0]);

        return view('admin.carnet.material-form', ['item' => $item, 'modo' => 'crear']);
    }

    public function storeMaterial(Request $request)
    {
        $data = $request->validate([
            'titulo'     => 'required|string|max:255',
            'subtitulo'  => 'nullable|string|max:100',
            'url'        => 'nullable|url|max:500',
            'tipo_boton' => 'required|in:descargar,ver',
            'orden'      => 'nullable|integer|min:0|max:9999',
            'pdf'        => 'nullable|file|mimes:pdf|max:51200',
        ]);

        $data['activo'] = true;
        $data['orden']  = $data['orden'] ?? 0;
        unset($data['pdf']);

        $item = CarnetMaterial::create($data);

        if ($request->hasFile('pdf')) {
            $item->url = $this->guardarPdf($request->file('pdf'), $item->id);
            $item->save();
        }

        AuditLog::registrar('crear', 'CarnetMaterial', $item->id, $item->titulo);

        return redirect()->route('admin.carnet.index')
            ->with('ok', 'Material creado correctamente.');
    }

    public function editMaterial(CarnetMaterial $material)
    {
        return view('admin.carnet.material-form', ['item' => $material, 'modo' => 'editar']);
    }

    public function updateMaterial(Request $request, CarnetMaterial $material)
    {
        $data = $request->validate([
            'titulo'     => 'required|string|max:255',
            'subtitulo'  => 'nullable|string|max:100',
            'url'        => 'nullable|url|max:500',
            'tipo_boton' => 'required|in:descargar,ver',
            'orden'      => 'nullable|integer|min:0|max:9999',
            'pdf'        => 'nullable|file|mimes:pdf|max:51200',
        ]);

        $data['orden'] = $data['orden'] ?? 0;
        unset($data['pdf']);

        if ($request->hasFile('pdf')) {
            $this->eliminarPdfAnterior($material->url);
            $data['url'] = $this->guardarPdf($request->file('pdf'), $material->id);
        }

        $material->update($data);

        AuditLog::registrar('editar', 'CarnetMaterial', $material->id, $material->titulo);

        return redirect()->route('admin.carnet.index')
            ->with('ok', 'Material actualizado correctamente.');
    }

    public function destroyMaterial(CarnetMaterial $material)
    {
        $this->eliminarPdfAnterior($material->url);

        $titulo = $material->titulo;
        $id     = $material->id;
        $material->delete();

        AuditLog::registrar('eliminar', 'CarnetMaterial', $id, $titulo);

        return redirect()->route('admin.carnet.index')
            ->with('ok', 'Material eliminado.');
    }

    public function toggleMaterial(CarnetMaterial $material)
    {
        $material->update(['activo' => ! $material->activo]);

        AuditLog::registrar('editar', 'CarnetMaterial', $material->id, $material->titulo);

        return redirect()->route('admin.carnet.index')
            ->with('ok', $material->activo ? 'Material activado.' : 'Material desactivado.');
    }

    private function guardarPdf($archivo, int $id): string
    {
        $directorio = public_path(self::PDF_DIR);
        File::ensureDirectoryExists($directorio);

        $nombreFinal = "carnet_material_{$id}_" . Str::random(12) . '.pdf';
        $archivo->move($directorio, $nombreFinal);

        return '/' . self::PDF_DIR . '/' . $nombreFinal;
    }

    private function eliminarPdfAnterior(?string $urlActual): void
    {
        if (! $urlActual || ! str_starts_with($urlActual, '/' . self::PDF_DIR . '/')) {
            return;
        }

        $ruta = public_path(self::PDF_DIR . '/' . basename($urlActual));
        if (File::exists($ruta)) {
            File::delete($ruta);
        }
    }
}
