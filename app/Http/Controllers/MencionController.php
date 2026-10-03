<?php

namespace App\Http\Controllers;

use App\Models\Mencion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MencionController extends Controller
{
    /**
     * Muestra el calendario de menciones filtrado por mes y año.
     */
    public function index(Request $request)
    {
        $mes = $request->input('mes', now('America/Mexico_City')->format('m'));
        $ano = $request->input('ano', now('America/Mexico_City')->format('Y'));

        // Calcular días del mes y el primer día de la semana
        $diasEnMes = \Carbon\Carbon::createFromDate($ano, $mes, 1)->daysInMonth;
        $primerDiaSemana = date('w', strtotime("$ano-$mes-01"));

        // Obtener las menciones de ese mes y año ordenadas
        $menciones = Mencion::whereYear('fecha', $ano)
            ->whereMonth('fecha', $mes)
            ->get()
            ->groupBy('fecha');

        return view('menciones.index', compact('menciones', 'mes', 'ano', 'diasEnMes', 'primerDiaSemana'));
    }

    /**
     * Almacena, actualiza, marca, desmarca o elimina una mención individual.
     */
    public function storeOrUpdate(Request $request)
    {
        $accion = $request->input('accion', 'guardar');
        $mencionId = $request->input('mencion_id');

       // 1. ACCIÓN: MARCAR MENCIÓN
        if ($accion === 'marcar') {
            $mencion = Mencion::findOrFail($mencionId);
            $horaManual = $request->input('hora_manual');

            if (!empty($horaManual)) {
                try {
                    // Si escribiste hora manual, se respeta tal cual
                    $mencion->marcado_at = Carbon::parse($mencion->fecha . ' ' . $horaManual, 'America/Mexico_City');
                } catch (\Exception $e) {
                    $mencion->marcado_at = Carbon::now('America/Mexico_City');
                }
            } else {
                // Usamos la hora actual exacta sin restar ninguna hora
                $mencion->marcado_at = Carbon::now('America/Mexico_City');
            }

            $mencion->save();
            return back()->with('success', '¡Mención marcada correctamente!');
        }

        // 2. ACCIÓN: DESMARCAR MENCIÓN
        if ($accion === 'desmarcar') {
            $mencion = Mencion::findOrFail($mencionId);
            $mencion->marcado_at = null;
            $mencion->save();
            return back()->with('success', 'Mención desmarcada.');
        }

        // 3. ACCIÓN: ELIMINAR MENCIÓN
        if ($accion === 'eliminar') {
            $mencion = Mencion::findOrFail($mencionId);
            $mencion->delete();
            return back()->with('success', 'Mención eliminada correctamente.');
        }

        // 4. ACCIÓN: GUARDAR O ACTUALIZAR TEXTO/LOCUTOR
        if ($mencionId) {
            $mencion = Mencion::findOrFail($mencionId);
            $mencion->locutor = $request->input('locutor');
            $mencion->texto = $request->input('texto');
            $mencion->save();
            
            return back()->with('success', 'Mención actualizada exitosamente.');
        } else {
            $request->validate([
                'fecha' => 'required|date',
                'locutor' => 'required|string',
                'texto' => 'required|string',
            ]);

            Mencion::create([
                'fecha' => $request->input('fecha'),
                'locutor' => $request->input('locutor'),
                'texto' => $request->input('texto'),
                'marcado_at' => null,
            ]);

            return back()->with('success', 'Mención creada exitosamente.');
        }
    }

    /**
     * Agrega menciones masivas a múltiples días seleccionados mediante Flatpickr.
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'locutor' => 'required|string',
            'texto' => 'required|string',
            'fechas' => 'required|string',
        ]);

        $locutor = $request->input('locutor');
        $texto = $request->input('texto');
        $fechasString = $request->input('fechas');

        $fechasArray = explode(',', $fechasString);

        foreach ($fechasArray as $fecha) {
            $fechaLimpia = trim($fecha);
            if (!empty($fechaLimpia)) {
                Mencion::create([
                    'fecha' => $fechaLimpia,
                    'locutor' => $locutor,
                    'texto' => $texto,
                    'marcado_at' => null,
                ]);
            }
        }

        return back()->with('success', '¡Menciones múltiples cargadas correctamente!');
    }
}