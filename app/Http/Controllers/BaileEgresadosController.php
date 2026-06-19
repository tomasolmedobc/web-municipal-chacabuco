<?php

namespace App\Http\Controllers;

use App\Models\BaileLugar;
use App\Models\BaileReserva;
use App\Models\BaileUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaileEgresadosController extends Controller
{
    public function index()
    {
        $totalAsientos   = BaileLugar::count();
        $disponibles     = BaileLugar::where('disponible', true)->count();
        $reservados      = $totalAsientos - $disponibles;

        return view('baile-egresados.index', compact('totalAsientos', 'disponibles', 'reservados'));
    }

    public function reservarForm()
    {
        $lugares = BaileLugar::where('disponible', true)
            ->orderBy('color')
            ->orderBy('fila')
            ->orderBy('numero')
            ->get();

        return view('baile-egresados.reservar', compact('lugares'));
    }

    public function guardarReserva(Request $request)
    {
        $request->validate([
            'dni'      => ['required', 'digits_between:7,9'],
            'codigo'   => ['required', 'string', 'size:8'],
            'asiento1' => ['required', 'exists:baile_lugares,id'],
            'asiento2' => ['nullable', 'exists:baile_lugares,id', 'different:asiento1'],
        ], [
            'dni.required'      => 'Ingresá tu DNI.',
            'dni.digits_between' => 'El DNI debe tener entre 7 y 9 dígitos.',
            'codigo.required'   => 'Ingresá el código de validación.',
            'codigo.size'       => 'El código debe tener exactamente 8 caracteres.',
            'asiento1.required' => 'Seleccioná al menos el primer asiento.',
            'asiento2.different' => 'No podés seleccionar el mismo asiento dos veces.',
        ]);

        $usuario = BaileUsuario::where('dni', $request->dni)
            ->where('codigo', $request->codigo)
            ->first();

        if (! $usuario) {
            return back()
                ->withErrors(['codigo' => 'El código ingresado no corresponde a ese DNI.'])
                ->withInput();
        }

        $asientos = collect([$request->asiento1]);
        if ($request->filled('asiento2')) {
            $asientos->push($request->asiento2);
        }
        $cantidad = $asientos->count();

        if ($usuario->disponibles < $cantidad) {
            return back()
                ->withErrors(['asiento1' => "No podés superar el límite. Te quedan {$usuario->disponibles} asiento(s) disponible(s) y elegiste {$cantidad}."])
                ->withInput();
        }

        $asientosReservados = [];

        try {
            DB::transaction(function () use ($usuario, $asientos, $cantidad, &$asientosReservados) {
                foreach ($asientos as $id) {
                    $lugar = BaileLugar::lockForUpdate()->find($id);

                    if (! $lugar || ! $lugar->disponible) {
                        throw new \RuntimeException('Uno de los asientos ya no está disponible. Seleccioná otro.');
                    }

                    BaileReserva::create([
                        'id_usuario' => $usuario->id,
                        'id_lugar'   => $lugar->id,
                        'pago'       => false,
                    ]);

                    $lugar->update(['disponible' => false]);
                    $asientosReservados[] = $lugar->descripcion;
                }

                $usuario->decrement('disponibles', $cantidad);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['asiento1' => $e->getMessage()])->withInput();
        }

        return redirect()->route('baile-egresados.confirmacion')
            ->with('nombre', $usuario->nombre_completo)
            ->with('asientos', $asientosReservados);
    }

    public function confirmacion()
    {
        if (! session()->has('nombre')) {
            return redirect()->route('baile-egresados.index');
        }

        return view('baile-egresados.confirmacion');
    }

    public function consultarForm()
    {
        return view('baile-egresados.consultar');
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'dni' => ['required', 'digits_between:7,9'],
        ], [
            'dni.required'       => 'Ingresá tu DNI.',
            'dni.digits_between' => 'El DNI debe tener entre 7 y 9 dígitos.',
        ]);

        $usuario = BaileUsuario::where('dni', $request->dni)->first();

        $reservas = $usuario
            ? BaileReserva::with('lugar')
                ->where('id_usuario', $usuario->id)
                ->get()
            : collect();

        return view('baile-egresados.consultar', compact('reservas', 'usuario'));
    }
}
