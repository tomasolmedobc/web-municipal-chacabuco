<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Modelo con el que se registran los eventos de acceso (login, logout, intentos fallidos y bloqueos)
    private const MODELO_ACCESO = 'Acceso';

    public function index(Request $request)
    {
        $tab = $request->input('tab') === 'accesos' ? 'accesos' : 'cambios';

        $base = AuditLog::query();
        $tab === 'accesos'
            ? $base->where('modelo', self::MODELO_ACCESO)
            : $base->where('modelo', '!=', self::MODELO_ACCESO);

        $query = (clone $base)->latest('created_at')->latest('id');

        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }

        if ($tab === 'cambios' && $request->filled('modelo')) {
            $query->where('modelo', $request->input('modelo'));
        }

        if ($request->filled('q')) {
            $busqueda = $request->input('q');
            $query->where(function ($q) use ($busqueda) {
                $q->where('descripcion', 'like', "%{$busqueda}%")
                    ->orWhere('user_nombre', 'like', "%{$busqueda}%");
            });
        }

        $logs = $query->paginate(30)->appends($request->query());

        $acciones = (clone $base)->distinct()->orderBy('accion')->pluck('accion');
        $modelos  = $tab === 'cambios'
            ? (clone $base)->distinct()->orderBy('modelo')->pluck('modelo')
            : collect();

        $totales = [
            'cambios' => AuditLog::where('modelo', '!=', self::MODELO_ACCESO)->count(),
            'accesos' => AuditLog::where('modelo', self::MODELO_ACCESO)->count(),
        ];

        return view('admin.audit-log.index', compact('logs', 'acciones', 'modelos', 'tab', 'totales'));
    }
}
