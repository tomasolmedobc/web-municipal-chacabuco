<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::latest('created_at');

        if ($request->filled('accion')) {
            $query->where('accion', $request->input('accion'));
        }

        if ($request->filled('modelo')) {
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

        $acciones = AuditLog::distinct()->orderBy('accion')->pluck('accion');
        $modelos  = AuditLog::distinct()->orderBy('modelo')->pluck('modelo');

        return view('admin.audit-log.index', compact('logs', 'acciones', 'modelos'));
    }
}
