<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Alumno;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ----- RANGO: últimos 7 días -----
        $hoy   = Carbon::today();
        $desde = $hoy->copy()->subDays(6);

        // Agregamos por fecha
        $rows = Asistencia::select(
                'fecha',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN estado = "TEMPRANO" THEN 1 ELSE 0 END) as tempranos'),
                DB::raw('SUM(CASE WHEN estado = "TARDE" THEN 1 ELSE 0 END) as tardanzas')
            )
            ->whereBetween('fecha', [$desde, $hoy])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $labels          = [];
        $datosPresentes  = [];
        $datosTardanzas  = [];

        foreach ($rows as $row) {
            $labels[] = Carbon::parse($row->fecha)->format('d/m');

            $total = max($row->total, 1); // para evitar división entre 0

            // Línea 1: asistencia positiva (TEMPRANO + TARDE) en %
            $presentes = (($row->tempranos + $row->tardanzas) / $total) * 100;

            // Línea 2: tardanzas en %
            $tardes = ($row->tardanzas / $total) * 100;

            $datosPresentes[] = round($presentes, 1);
            $datosTardanzas[] = round($tardes, 1);
        }

        // Promedio general de asistencia
        $porcentajeAsistenciaGeneral = count($datosPresentes)
            ? round(array_sum($datosPresentes) / count($datosPresentes), 1)
            : 0;

        // ----- Ranking de alumnos más puntuales -----
        $rankingPuntualidad = Alumno::select(
                'alumnos.*',
                DB::raw('COUNT(asistencias.id) as total_asistencias'),
                DB::raw('SUM(CASE WHEN asistencias.estado = "TEMPRANO" THEN 1 ELSE 0 END) as asistencias_tempranas'),
                DB::raw('CASE WHEN COUNT(asistencias.id) = 0 
                          THEN 0 
                          ELSE (SUM(CASE WHEN asistencias.estado = "TEMPRANO" THEN 1 ELSE 0 END) 
                                / COUNT(asistencias.id)) * 100 
                     END as porcentaje_puntualidad')
            )
            ->leftJoin('asistencias', 'asistencias.alumno_id', '=', 'alumnos.id')
            ->groupBy('alumnos.id')
            ->having('total_asistencias', '>', 0)
            ->orderByDesc('porcentaje_puntualidad')
            ->limit(5)
            ->get();

        return view('alumnos.index', [
            'labelsChart'                 => $labels,
            'datosPresentes'              => $datosPresentes,
            'datosTardanzas'              => $datosTardanzas,
            'porcentajeAsistenciaGeneral' => $porcentajeAsistenciaGeneral,
            'rankingPuntualidad'          => $rankingPuntualidad,
        ]);
    }
}
