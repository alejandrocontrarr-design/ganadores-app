<?php

namespace App\Exports;

use App\Models\Mencion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class MencionesMesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $mes;
    protected $ano;

    public function __construct($mes, $ano)
    {
        $this->mes = $mes;
        $this->ano = $ano;
    }

    public function collection()
    {
        // Orden personalizado de los locutores según tu programación exacta
        $ordenLocutores = [
            'León en Exa',
            'Gsus con G',
            'CB Noticias',
            'Sin Filtros',
            'Aquí Entre Nos',
            'Reconexión',
            'El Merequetengue',
            '33/45'
        ];

        $fieldString = "'" . implode("', '", $ordenLocutores) . "'";

        return Mencion::whereYear('fecha', $this->ano)
                      ->whereMonth('fecha', $this->mes)
                      ->orderBy('fecha', 'asc')
                      ->orderByRaw("FIELD(locutor, {$fieldString})")
                      ->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Día',
            'Locutor / Programa',
            'Texto de la Mención',
            'Estado',
            'Hora de Marcado'
        ];
    }

    public function map($mencion): array
    {
        $fechaCarbon = Carbon::parse($mencion->fecha);
        $fechaCarbon->setLocale('es');
        $nombreDia = ucfirst($fechaCarbon->dayName);

        $marcado = !is_null($mencion->marcado_at);
        
        return [
            $mencion->fecha,
            $nombreDia,
            $mencion->locutor,
            $mencion->texto,
            $marcado ? 'Completado' : 'Pendiente',
            $marcado ? Carbon::parse($mencion->marcado_at)->format('h:i A') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo de la cabecera principal (A hasta F)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '8B736C'], // Color café acorde a tu diseño
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Recorrer filas para aplicar formato dinámico (Verde suave si está completado)
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $estado = $sheet->getCell("E{$row}")->getValue();
            
            if ($estado === 'Completado') {
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'D1E7DD'], // Verde suave (.dia-completado)
                    ],
                    'font' => [
                        'color' => ['argb' => '0F5132'],
                    ]
                ]);
            }
        }

        return [];
    }
}