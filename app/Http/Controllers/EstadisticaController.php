<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Asistencia;

class EstadisticaController extends Controller
{
    public function index(Request $request)
    {
        // 1) Obtener salones (grado + sección)
        $salones = Alumno::with(['grado', 'seccion'])
            ->select('grado_id', 'seccion_id')
            ->groupBy('grado_id', 'seccion_id')
            ->orderBy('grado_id')
            ->orderBy('seccion_id')
            ->get()
            ->map(function ($al) {
                $gradoNombre   = trim($al->grado->nombre ?? '');
                $seccionNombre = trim($al->seccion->nombre ?? '');

                return (object) [
                    'grado_id'       => $al->grado_id,
                    'seccion_id'     => $al->seccion_id,
                    'grado_nombre'   => $gradoNombre,
                    'seccion_nombre' => $seccionNombre,
                    'label'          => ($gradoNombre !== '' ? $gradoNombre . '° ' : '') . $seccionNombre,
                ];
            })
            ->unique(fn ($x) => $x->grado_id . '-' . $x->seccion_id)
            ->values();

        if ($salones->isEmpty()) {
            return view('estadisticas.index', [
                'salones'        => collect(),
                'grado_id'       => null,
                'seccion_id'     => null,
                'salonLabel'     => 'Sin salones',
                'resumenSalon'   => ['puntualidad' => 0, 'tardanzas' => 0, 'faltas' => 0, 'total' => 0],
                'generoSalon'    => ['masculino' => 0, 'femenino' => 0],
                'gradosLista'    => collect(),
                'seccionesLista' => collect(),
                'conteoDias'     => [],
                'asistenciaMes'  => [],
                'topTardes'      => [],
            ]);
        }

        // 2) Selección de grado y sección
        $default    = $salones->first();
        $grado_id   = (int) $request->input('grado_id',   $default->grado_id);
        $seccion_id = (int) $request->input('seccion_id', $default->seccion_id);

        $salonSel = $salones->first(fn ($s) =>
            $s->grado_id == $grado_id && $s->seccion_id == $seccion_id
        ) ?? $default;

        $salonLabel = $salonSel->label;

        // 3) Lista de grados
        $gradosLista = $salones
            ->map(fn ($s) => (object)[
                'id'     => $s->grado_id,
                'nombre' => $s->grado_nombre,
            ])
            ->unique('id')
            ->sortBy('id')
            ->values();


        // 4) Lista de secciones del grado seleccionado
        $seccionesLista = $salones
            ->where('grado_id', $grado_id)
            ->map(fn ($s) => (object)[
                'id'     => $s->seccion_id,
                'nombre' => $s->seccion_nombre,
            ])
            ->unique('id')
            ->sortBy('nombre')
            ->values();


        // 5) Resumen de asistencia
        $baseQuery = Asistencia::whereHas('alumno', function ($q) use ($grado_id, $seccion_id) {
            $q->where('grado_id', $grado_id)
              ->where('seccion_id', $seccion_id);
        });

        $total     = (clone $baseQuery)->count();
        $presentes = (clone $baseQuery)->where('estado', 'ASISTIÓ')->count();
        $tardes    = (clone $baseQuery)->where('estado', 'TARDE')->count();
        $faltas    = (clone $baseQuery)->where('estado', 'FALTÓ')->count();

        $resumenSalon = [
            'puntualidad' => $total ? round(($presentes / $total) * 100, 1) : 0,
            'tardanzas'   => $total ? round(($tardes    / $total) * 100, 1) : 0,
            'faltas'      => $total ? round(($faltas    / $total) * 100, 1) : 0,
            'total'       => $total,
        ];


        // 6) Género
        $masc = Alumno::where('grado_id', $grado_id)
            ->where('seccion_id', $seccion_id)
            ->whereIn('genero', ['M', 'MASCULINO'])
            ->count();

        $fem = Alumno::where('grado_id', $grado_id)
            ->where('seccion_id', $seccion_id)
            ->whereIn('genero', ['F', 'FEMENINO'])
            ->count();

        $generoSalon = [
            'masculino' => $masc,
            'femenino'  => $fem,
        ];


        // -------------------------
        // 7) Nuevos gráficos
        // -------------------------

        // 7.1) Asistencia por día de la semana
        $diasSemana = ['Lunes','Martes','Miércoles','Jueves','Viernes'];
        $conteoDias = [];

        foreach ($diasSemana as $i => $dia) {
            $conteoDias[] = Asistencia::whereHas('alumno', function ($q) use ($grado_id,$seccion_id) {
                    $q->where('grado_id',$grado_id)->where('seccion_id',$seccion_id);
                })
                ->whereRaw("DAYOFWEEK(fecha)=?", [$i+2])
                ->count();
        }

        // 7.2) Tendencia mensual
        $asistenciaMes = [];

        for ($m = 1; $m <= 12; $m++) {

            $mesTotal = Asistencia::whereHas('alumno', function ($q) use ($grado_id,$seccion_id){
                    $q->where('grado_id',$grado_id)->where('seccion_id',$seccion_id);
                })
                ->whereMonth('fecha',$m)
                ->count();

            $mesPresentes = Asistencia::whereHas('alumno', function ($q) use ($grado_id,$seccion_id){
                    $q->where('grado_id',$grado_id)->where('seccion_id',$seccion_id);
                })
                ->whereMonth('fecha',$m)
                ->where('estado','ASISTIÓ')
                ->count();

            $asistenciaMes[] = $mesTotal ? round(($mesPresentes/$mesTotal)*100,1) : 0;
        }

        // 7.3) Top 10 tardanzas
        $topTardes = Asistencia::with('alumno')
            ->where('estado','TARDE')
            ->whereHas('alumno', fn($q)=>$q->where('grado_id',$grado_id)->where('seccion_id',$seccion_id))
            ->selectRaw('alumno_id, COUNT(*) AS total')
            ->groupBy('alumno_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($r)=>[
                'nombre'=>$r->alumno->nombre_apellido,
                'total' =>$r->total
            ]);


        // 8) Devolver vista
        return view('estadisticas.index', compact(
            'salones',
            'grado_id',
            'seccion_id',
            'salonLabel',
            'resumenSalon',
            'generoSalon',
            'gradosLista',
            'seccionesLista',

            'conteoDias',
            'asistenciaMes',
            'topTardes'
        ));
    }
}
