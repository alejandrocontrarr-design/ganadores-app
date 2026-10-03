<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mencion;
use App\Exports\MencionesMesExport;
use Maatwebsite\Excel\Facades\Excel;

class LimpiezaMencionesController extends Controller
{
    /**
     * Limpia (elimina) todas las menciones de un mes y año específicos.
     */
    public function limpiarMes(Request $request)
    {
        // Validación de seguridad para evitar que el invitado ejecute esta acción
        if (auth()->user()->email === 'exa@invitado.com') {
            return back()->withErrors(['error' => 'No tienes permisos para realizar esta acción.']);
        }

        $mes = $request->input('mes');
        $ano = $request->input('ano');

        $nombreMeses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        $nombreMesTexto = $nombreMeses[$mes] ?? $mes;

        // Verificar si existen menciones para ese mes y año
        $existenMenciones = Mencion::whereYear('fecha', $ano)
                                   ->whereMonth('fecha', $mes)
                                   ->exists();

        if (!$existenMenciones) {
            return redirect()->route('menciones.index', ['mes' => $mes, 'ano' => $ano])
                             ->withErrors(['error' => "No se encontraron menciones registradas para el mes de {$nombreMesTexto} de {$ano}, por lo que no hay nada que limpiar."]);
        }

        // Borrar las menciones que coincidan con el año y mes especificados
        Mencion::whereYear('fecha', $ano)
               ->whereMonth('fecha', $mes)
               ->delete();

        return redirect()->route('menciones.index', ['mes' => $mes, 'ano' => $ano])
                         ->with('success', "Se han limpiado todas las menciones del mes de {$nombreMesTexto} del año {$ano}.");
    }

    /**
     * Exporta el calendario de menciones de un mes y año en formato Excel ordenado.
     */
    public function exportarMes(Request $request)
    {
        $mes = $request->input('mes');
        $ano = $request->input('ano');
        
        $nombreMeses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        $nombreMesTexto = $nombreMeses[$mes] ?? $mes;

        // Verificar si existen menciones antes de exportar
        $existenMenciones = Mencion::whereYear('fecha', $ano)
                                   ->whereMonth('fecha', $mes)
                                   ->exists();

        if (!$existenMenciones) {
            return redirect()->route('menciones.index', ['mes' => $mes, 'ano' => $ano])
                             ->withErrors(['error' => "No hay menciones registradas en el mes de {$nombreMesTexto} de {$ano} para exportar."]);
        }
        
        $nombreArchivo = "Menciones_{$nombreMesTexto}_{$ano}.xlsx";

        return Excel::download(new MencionesMesExport($mes, $ano), $nombreArchivo);
    }
}