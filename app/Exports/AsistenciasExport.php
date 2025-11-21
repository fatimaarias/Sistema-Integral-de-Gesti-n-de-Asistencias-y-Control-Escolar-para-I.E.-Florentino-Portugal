<?php

namespace App\Exports;

use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsistenciasExport implements FromQuery, WithHeadings, WithMapping
{
    protected $grados;
    protected $secciones;
    protected $fecha;

    // 👇 ESTE ES EL CONSTRUCTOR QUE ME MOSTRASTE
    public function __construct($grados, $secciones, $fecha)
    {
        $this->grados    = $grados;
        $this->secciones = $secciones;
        $this->fecha     = $fecha;
    }

    // 👇 AQUÍ VA EL query() QUE ME MOSTRASTE
    public function query()
    {
        return Asistencia::query()
            ->whereDate('fecha', $this->fecha)
            ->when($this->grados, function ($q) {
                $q->whereHas('alumno', function ($a) {
                    $a->whereIn('grado_id', $this->grados);
                });
            })
            ->when($this->secciones, function ($q) {
                $q->whereHas('alumno', function ($a) {
                    $a->whereIn('seccion_id', $this->secciones);
                });
            })
            ->with('alumno.grado', 'alumno.seccion');
    }

    public function headings(): array
    {
        return ['Fecha', 'Alumno', 'Grado', 'Sección', 'Estado'];
    }

    public function map($a): array
    {
        return [
            $a->fecha,
            $a->alumno->nombre_apellido ?? '',
            $a->alumno->grado->nombre   ?? '',
            $a->alumno->seccion->nombre ?? '',
            $a->estado,
        ];
    }
}
