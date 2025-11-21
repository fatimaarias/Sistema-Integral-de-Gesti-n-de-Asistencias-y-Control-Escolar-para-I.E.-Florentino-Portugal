<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\Asistencia;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AlumnosImport;


class AlumnoController extends Controller
{
    // =================== DASHBOARD (alumnos.index) ===================
  // app/Http/Controllers/AlumnoController.php

public function index()
{
    // Total de alumnos (para calcular porcentajes)
    $totalAlumnos = Alumno::count();
    if ($totalAlumnos === 0) {
        $totalAlumnos = 1; // evita división entre 0
    }

    // === SERIE PARA EL GRÁFICO (últimos 7 días con datos) ===
    $serie = Asistencia::select(
            'fecha',
            DB::raw("SUM(CASE WHEN estado = 'ASISTIÓ' THEN 1 ELSE 0 END) AS presentes"),
            DB::raw("SUM(CASE WHEN estado = 'TARDE'   THEN 1 ELSE 0 END) AS tardes")
        )
        ->groupBy('fecha')
        ->orderBy('fecha', 'asc')
        ->limit(7) // máximo 7 días
        ->get();

    $labelsChart        = [];
    $asistenciaPositiva = [];   // línea azul
    $tardanzasChart     = [];   // línea naranja

    foreach ($serie as $row) {
        $labelsChart[]        = Carbon::parse($row->fecha)->format('d/m');
        $asistenciaPositiva[] = round(($row->presentes / $totalAlumnos) * 100, 1);
        $tardanzasChart[]     = round(($row->tardes    / $totalAlumnos) * 100, 1);
    }

    // Si aún no hay datos en la tabla, mostramos algo neutro
    if (empty($labelsChart)) {
        $labelsChart        = ['—'];
        $asistenciaPositiva = [0];
        $tardanzasChart     = [0];
    }

    // === Asistencia general (solo presentes "ASISTIÓ") ===
    $totalDias    = max(count($labelsChart), 1);
    $totalPosible = $totalAlumnos * $totalDias;

    $totalPresentes = $serie->sum('presentes');
    $porcentajeAsistenciaGeneral = $totalPosible > 0
        ? round(($totalPresentes / $totalPosible) * 100, 1)
        : 0;

    // === Ranking de puntualidad (top 5 alumnos) ===
    $rankingPuntualidad = Alumno::select(
            'alumnos.*',
            DB::raw("COUNT(asistencias.id) AS total_registros"),
            DB::raw("SUM(CASE WHEN asistencias.estado = 'ASISTIÓ' THEN 1 ELSE 0 END) AS total_presentes"),
            DB::raw("
                CASE 
                  WHEN COUNT(asistencias.id) = 0 THEN 0
                  ELSE (SUM(CASE WHEN asistencias.estado = 'ASISTIÓ' THEN 1 ELSE 0 END) / COUNT(asistencias.id)) * 100
                END AS porcentaje_puntualidad
            ")
        )
        ->leftJoin('asistencias', 'asistencias.alumno_id', '=', 'alumnos.id')
        ->groupBy('alumnos.id')
        ->orderByDesc('porcentaje_puntualidad')
        ->limit(5)
        ->get();

    // === NUEVO: Ranking de secciones más puntuales (Top 3) ===
    // PUNTUALIDAD = % de registros con estado = 'ASISTIÓ' sobre el total de asistencias de la sección
    $rankingSecciones = Seccion::select(
            'secciones.id',
            'secciones.nombre as seccion_nombre',
            'grados.nombre as grado_nombre',
            DB::raw('COUNT(asistencias.id) AS total_registros'),
            DB::raw("SUM(CASE WHEN asistencias.estado = 'ASISTIÓ' THEN 1 ELSE 0 END) AS total_presentes"),
            DB::raw("
                CASE 
                    WHEN COUNT(asistencias.id) = 0 THEN 0
                    ELSE (SUM(CASE WHEN asistencias.estado = 'ASISTIÓ' THEN 1 ELSE 0 END) / COUNT(asistencias.id)) * 100
                END AS porcentaje_puntualidad
            ")
        )
        ->join('alumnos', 'alumnos.seccion_id', '=', 'secciones.id')
        ->join('grados', 'grados.id', '=', 'alumnos.grado_id')
        ->leftJoin('asistencias', 'asistencias.alumno_id', '=', 'alumnos.id')
        ->groupBy('secciones.id', 'secciones.nombre', 'grados.nombre')
        ->orderByDesc('porcentaje_puntualidad')
        ->limit(3)
        ->get();

    // Para mostrar/ocultar la tarjeta de "Gestionar usuarios"
    $isAdmin = auth()->check() && (
        (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin')) ||
        (isset(auth()->user()->role) && auth()->user()->role === 'admin')
    );

    return view('alumnos.index', compact(
        'labelsChart',
        'asistenciaPositiva',
        'tardanzasChart',
        'porcentajeAsistenciaGeneral',
        'rankingPuntualidad',
        'rankingSecciones',  // 👈 importante
        'isAdmin'
    ));
}


    // =================== SELECCIONAR GRADO/SECCIÓN ===================
    // GET alumnos.seleccionar -> grid de 10 tarjetas (1°A ... 5°B)
    public function seleccionar()
    {
        $grados = Grado::all();

        if (auth()->user()->role === 'auxiliar') {
            // Solo las secciones asignadas al auxiliar (A o B)
            $ids       = auth()->user()->secciones()->pluck('secciones.id');
            $secciones = Seccion::whereIn('id', $ids)->get();
        } else {
            // Admin / Secundario ven todas
            $secciones = Seccion::all();
        }

        return view('alumnos.seleccionar', compact('grados', 'secciones'));
    }

    // =================== CREAR ALUMNO ===================
    // GET alumnos.create?grado_id=&seccion_id=
    public function create(Request $request)
    {
        $grados    = Grado::all();
        $secciones = Seccion::all(); // A y B globales

        // Pre-selección si vienen en query
        $gradoId   = $request->query('grado_id');
        $seccionId = $request->query('seccion_id');

        return view('alumnos.create', compact('grados', 'secciones', 'gradoId', 'seccionId'));
    }

    // POST alumnos.store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres'    => 'required|string|max:255',
            'direccion'  => 'nullable|string|max:255',
            'genero'     => 'required|in:M,F',
            'grado_id'   => 'required|exists:grados,id',
            'seccion_id' => 'required|exists:secciones,id',
        ]);

        Alumno::create([
            'nombre_apellido' => $validated['nombres'],
            'direccion'       => $validated['direccion'] ?? null,
            'genero'          => $validated['genero'] === 'M' ? 'MASCULINO' : 'FEMENINO',
            'grado_id'        => $validated['grado_id'],
            'seccion_id'      => $validated['seccion_id'],
        ]);

        return redirect()
            ->route('alumnos.ver', [
                'grado'   => $validated['grado_id'],
                'seccion' => $validated['seccion_id'],
            ])
            ->with('success', '✅ Alumno registrado correctamente.');
    }

    // =================== ELIMINAR ALUMNO ===================
    // DELETE alumnos.destroy/{id}
    public function destroy($id)
    {
        Alumno::findOrFail($id)->delete();
        return back()->with('success', 'Alumno eliminado correctamente.');
    }

    // =================== IMPORTAR ALUMNOS ===================
    // POST alumnos.importar
    public function importar(Request $request)
    {
        $request->validate([
            'archivo'    => 'required|mimes:xlsx,csv',
            'grado_id'   => 'required|exists:grados,id',
            'seccion_id' => 'required|exists:secciones,id',
        ]);

        Excel::import(new AlumnosImport($request->grado_id, $request->seccion_id), $request->file('archivo'));

        return back()->with('success', '✅ Alumnos importados correctamente para el grado y sección seleccionados.');
    }

    // =================== VER ALUMNOS POR SECCIÓN ===================
    // GET alumnos.ver/{grado}/{seccion}
    public function ver($grado_id, $seccion_id)
    {
        $grado   = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);

        $alumnos = Alumno::where('grado_id', $grado_id)
                        ->where('seccion_id', $seccion_id)
                        ->get();

        return view('alumnos.ver', compact('grado', 'seccion', 'alumnos'));
    }

    // =================== ELIMINAR MÚLTIPLES ALUMNOS ===================
    public function destroyMany(Request $request)
{
    $ids = $request->ids;

    if (!$ids || count($ids) == 0) {
        return back()->with('error', 'No seleccionaste ningún alumno.');
    }

    Alumno::whereIn('id', $ids)->delete();

    return back()->with('success', 'Alumnos eliminados correctamente.');
}


public function update(Request $request, $id)
{
    $alumno = Alumno::findOrFail($id);

    $request->validate([
        'nombre_apellido' => 'required|string|max:255',
        'genero' => 'nullable|string',
        'direccion' => 'nullable|string|max:255'
    ]);

    $alumno->update([
        'nombre_apellido' => $request->nombre_apellido,
        'genero'          => $request->genero,
        'direccion'       => $request->direccion,
    ]);

    return back()->with('success', 'Alumno actualizado correctamente.');
}

}
