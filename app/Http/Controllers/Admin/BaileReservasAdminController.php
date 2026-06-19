<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BaileReserva;
use Illuminate\Http\Request;

class BaileReservasAdminController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->get('q');
        $filtroPago = $request->get('pago', '');

        $reservas = BaileReserva::with(['usuario', 'lugar'])
            ->when($busqueda, fn ($q) => $q->whereHas('usuario', fn ($u) =>
                $u->where('nombre_completo', 'like', "%{$busqueda}%")
                  ->orWhere('dni', 'like', "%{$busqueda}%")
            ))
            ->when($filtroPago !== '', fn ($q) => $q->where('pago', (bool) $filtroPago))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->appends($request->query());

        $totales = [
            'total'  => BaileReserva::count(),
            'pagado' => BaileReserva::where('pago', true)->count(),
            'pendiente' => BaileReserva::where('pago', false)->count(),
        ];

        return view('admin.baile-egresados.reservas.index', compact('reservas', 'busqueda', 'filtroPago', 'totales'));
    }

    public function togglePago(BaileReserva $reserva)
    {
        $reserva->update(['pago' => ! $reserva->pago]);

        $estado = $reserva->pago ? 'pagado' : 'pendiente';
        AuditLog::registrar('editar', 'BaileReserva', $reserva->id, "Pago de reserva marcado como {$estado}: {$reserva->usuario?->nombre_completo}");

        return back()->with('ok', "Reserva marcada como {$estado}.");
    }

    public function destroy(BaileReserva $reserva)
    {
        $lugar = $reserva->lugar;
        AuditLog::registrar('eliminar', 'BaileReserva', $reserva->id, "Reserva eliminada: {$reserva->usuario?->nombre_completo} — {$lugar?->descripcion}");

        $reserva->delete();

        if ($lugar) {
            $lugar->update(['disponible' => true]);
            $reserva->usuario?->increment('disponibles');
        }

        return back()->with('ok', 'Reserva eliminada y asiento liberado.');
    }
}
