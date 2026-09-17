<?php

namespace App\Imports;

use App\Models\Ganador;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Carbon;

class GanadoresImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Ignorar títulos y encabezados de las primeras 5 filas
            if ($index < 5) {
                continue;
            }

            $data = $row->toArray();

            // Obtener valor por Letra o por Índice numérico
            $getVal = function ($letra, $numero) use ($data) {
                if (isset($data[$letra]) && $data[$letra] !== null && trim((string)$data[$letra]) !== '') {
                    return trim((string)$data[$letra]);
                }
                if (isset($data[$numero]) && $data[$numero] !== null && trim((string)$data[$numero]) !== '') {
                    return trim((string)$data[$numero]);
                }
                return null;
            };

            $nombre   = $getVal('A', 0);
            $edadRaw  = $getVal('C', 2);
            $whatsapp = $getVal('D', 3);
            $facebook = $getVal('F', 5);
            $fDinaRaw = $getVal('H', 7);
            $fEntrRaw = $getVal('I', 8);
            $programa = $getVal('J', 9);
            $premio   = $getVal('L', 11);
            $patrocin = $getVal('N', 13);

            // Saltar la fila si es el título de los encabezados
            if ($nombre && str_contains(strtolower($nombre), 'nombre del ganador')) {
                continue;
            }

            // Saltar filas totalmente vacías
            if (empty($nombre) && empty($whatsapp) && empty($programa)) {
                continue;
            }

            // Convertidor de fechas
            $parseFecha = function ($val) {
                if (empty($val)) return null;
                if (is_numeric($val)) {
                    try {
                        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val))->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                }
                try {
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            };

            Ganador::create([
                'nombre'         => $nombre ?? 'Sin Nombre',
                'edad'           => is_numeric($edadRaw) ? (int)$edadRaw : null,
                'whatsapp'       => $whatsapp,
                'facebook_id'    => $facebook,
                'fecha_dinamica' => $parseFecha($fDinaRaw),
                'fecha_entrega'  => $parseFecha($fEntrRaw),
                'programa'       => $programa,
                'premio'         => $premio,
                'patrocinador'   => $patrocin,
            ]);
        }
    }
}