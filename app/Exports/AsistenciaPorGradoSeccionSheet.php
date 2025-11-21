<?php

namespace App\Exports;

use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsistenciaPorGradoSeccionSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $grado;
    protected $seccion;
    protected $fecha;

    public function __construct($grado, $seccion, $fecha)
    {
        $this->grado   = $grado;
        $this->seccion = $seccion;
        $this->fecha   = $fecha;
    }

    public function collection()
    {
        return Asistencia::whereDate('fecha', $this->fecha)
            ->whereHas('alumno', function ($q) {
                $q->where('grado_id', $this->grado->id)
                  ->where('seccion_id', $this->seccion->id);
            })
            ->with('alumno.grado', 'alumno.seccion')
            ->get()
            ->map(function ($a) {
                return [
                    'Fecha'     => $a->fecha,
                    'Alumno'    => $a->alumno->nombre_apellido ?? '',
                    'Grado'     => $a->alumno->grado->nombre ?? '',
                    'Sección'   => $a->alumno->seccion->nombre ?? '',
                    'Estado'    => $a->estado,
                ];
            });
    }

    public function headings(): array
    {
        return ['Fecha', 'Alumno', 'Grado', 'Sección', 'Estado'];
    }

    public function title(): string
    {
        return $this->grado->nombre . '° ' . $this->seccion->nombre;
    }
}