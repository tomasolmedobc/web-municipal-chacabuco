<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RecaudacionDocumento;
use App\Models\RecaudacionTramiteOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RecaudacionAdminController extends Controller
{
    private const PDF_DIR = 'files/recaudacion';

    public function index()
    {
        $documentos = RecaudacionDocumento::orderBy('orden')->orderBy('id')->get();
        $tramite    = RecaudacionTramiteOnline::instancia();

        return view('admin.recaudacion.index', compact('documentos', 'tramite'));
    }

    public function create()
    {
        $item = new RecaudacionDocumento(['activo' => true, 'orden' => 0]);

        return view('admin.recaudacion.form', ['item' => $item, 'modo' => 'crear']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'url'    => 'nullable|url|max:500',
            'orden'  => 'nullable|integer|min:0|max:9999',
            'pdf'    => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data['activo'] = true;
        $data['orden']  = $data['orden'] ?? 0;
        unset($data['pdf']);

        $item = RecaudacionDocumento::create($data);

        if ($request->hasFile('pdf')) {
            $item->url = $this->guardarPdf($request->file('pdf'), $item->id);
            $item->save();
        }

        AuditLog::registrar('crear', 'RecaudacionDocumento', $item->id, $item->titulo);

        return redirect()->route('admin.recaudacion.index')
            ->with('ok', 'Documento creado correctamente.');
    }

    public function edit(RecaudacionDocumento $documento)
    {
        return view('admin.recaudacion.form', ['item' => $documento, 'modo' => 'editar']);
    }

    public function update(Request $request, RecaudacionDocumento $documento)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'url'    => 'nullable|url|max:500',
            'orden'  => 'nullable|integer|min:0|max:9999',
            'pdf'    => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data['orden'] = $data['orden'] ?? 0;
        unset($data['pdf']);

        if ($request->hasFile('pdf')) {
            $this->eliminarPdfAnterior($documento->id);
            $data['url'] = $this->guardarPdf($request->file('pdf'), $documento->id);
        }

        $documento->update($data);

        AuditLog::registrar('editar', 'RecaudacionDocumento', $documento->id, $documento->titulo);

        return redirect()->route('admin.recaudacion.index')
            ->with('ok', 'Documento actualizado correctamente.');
    }

    public function destroy(RecaudacionDocumento $documento)
    {
        $this->eliminarPdfAnterior($documento->id);

        $titulo = $documento->titulo;
        $id     = $documento->id;
        $documento->delete();

        AuditLog::registrar('eliminar', 'RecaudacionDocumento', $id, $titulo);

        return redirect()->route('admin.recaudacion.index')
            ->with('ok', 'Documento eliminado.');
    }

    public function toggle(RecaudacionDocumento $documento)
    {
        $documento->update(['activo' => ! $documento->activo]);

        AuditLog::registrar('editar', 'RecaudacionDocumento', $documento->id, $documento->titulo);

        return redirect()->route('admin.recaudacion.index')
            ->with('ok', $documento->activo ? 'Documento activado.' : 'Documento desactivado.');
    }

    public function editTramite()
    {
        $tramite = RecaudacionTramiteOnline::instancia();

        return view('admin.recaudacion.tramite', compact('tramite'));
    }

    public function updateTramite(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'url'         => 'nullable|url|max:500',
        ]);

        $tramite = RecaudacionTramiteOnline::instancia();
        $tramite->update($data);

        AuditLog::registrar('editar', 'RecaudacionTramiteOnline', $tramite->id, $tramite->titulo);

        return redirect()->route('admin.recaudacion.index')
            ->with('ok', 'Trámite online actualizado.');
    }

    private function guardarPdf($archivo, int $id): string
    {
        $directorio = public_path(self::PDF_DIR);
        File::ensureDirectoryExists($directorio);

        $nombreFinal = "recaudacion_{$id}.pdf";
        $archivo->move($directorio, $nombreFinal);

        return '/' . self::PDF_DIR . '/' . $nombreFinal;
    }

    private function eliminarPdfAnterior(int $id): void
    {
        $ruta = public_path(self::PDF_DIR . "/recaudacion_{$id}.pdf");
        if (File::exists($ruta)) {
            File::delete($ruta);
        }
    }
}
