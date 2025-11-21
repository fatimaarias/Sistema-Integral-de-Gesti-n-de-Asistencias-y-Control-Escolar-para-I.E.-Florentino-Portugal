<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Seccion;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AsistenciasExport;
use Illuminate\Support\Str; 
use Illuminate\Database\QueryException;

class AsistenciaController extends Controller
{



    private function cargarFiltrosGlobales()
{
    return [
        'gradosGlobal'    => Grado::all(),
        'seccionesGlobal' => Seccion::all(),
    ];
}


    /* =======================================================
     *  INDEX GENERAL
     * ======================================================= */
    public function index(Request $request)
    {
        $fecha      = $request->input('fecha', date('Y-m-d'));
        $grado_id   = $request->input('grado_id');
        $seccion_id = $request->input('seccion_id');

        $grados     = Grado::all();
        $secciones  = Seccion::all();

        $alumnosQ = Alumno::with(['grado','seccion'])->orderBy('nombre_apellido');

        if ($grado_id)   $alumnosQ->where('grado_id', $grado_id);
        if ($seccion_id) $alumnosQ->where('seccion_id', $seccion_id);

        $alumnos = $alumnosQ->get();
        $asistencias = Asistencia::whereDate('fecha', $fecha)->get()->keyBy('alumno_id');

        return view('asistencias.index', compact(
            'alumnos','grados','secciones','asistencias','fecha','grado_id','seccion_id'
        ));
    }

    /* =======================================================
     *  GUARDAR ASISTENCIAS
     * ======================================================= */
    public function store(Request $request)
    {
        $fecha   = $request->input('fecha');
        $estados = $request->input('estado', []);

        $permitidas = [];
        if (auth()->user()->role === 'auxiliar') {
            $permitidas = auth()->user()->secciones()->pluck('secciones.id')->toArray();
        }

        foreach ($estados as $alumno_id => $estado) {

            if (auth()->user()->role === 'auxiliar') {
                $alumno = Alumno::select('id','seccion_id')->find($alumno_id);
                if (!$alumno || !in_array($alumno->seccion_id, $permitidas)) {
                    abort(403, 'No autorizado para marcar esta sección.');
                }
            }

            Asistencia::updateOrCreate(
                ['alumno_id' => $alumno_id, 'fecha' => $fecha],
                ['estado'   => $estado]
            );
        }

        return back()->with('success', 'Asistencias registradas correctamente.');
    }

    /* =======================================================
     *  GRID DE 10 TARJETAS
     * ======================================================= */
    public function seleccionar()
    {
        $grados = Grado::all();

        $secciones = Seccion::query()
            ->when(auth()->user()->role === 'auxiliar', function ($q) {
                $ids = auth()->user()->secciones()->pluck('secciones.id');
                $q->whereIn('id', $ids);
            })
            ->get();

        $fecha = date('Y-m-d');

        return view('asistencias.seleccionar', compact('grados','secciones','fecha'));
    }

    /* =======================================================
     *  VER ASISTENCIAS POR SECCIÓN
     * ======================================================= */
    public function ver(Request $request, $grado_id, $seccion_id)
    {
        $fecha   = $request->query('fecha', Carbon::now()->toDateString());
        $grado   = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);

        $alumnos = Alumno::with([
            'grado','seccion',
            'asistencias' => function ($q) use ($fecha) {
                $q->whereDate('fecha', $fecha);
            }
        ])
        ->where('grado_id', $grado_id)
        ->where('seccion_id', $seccion_id)
        ->orderBy('nombre_apellido')
        ->get();

        return view('asistencias.ver', compact('grado','seccion','alumnos','fecha'));
    }

    public function seleccionarAgregar()
    {
        $grados = Grado::all();

        $secciones = Seccion::query()
            ->when(auth()->user()->role === 'auxiliar', function ($q) {
                $ids = auth()->user()->secciones()->pluck('secciones.id');
                $q->whereIn('id', $ids);
            })
            ->get();

        return view('asistencias.agregarSeleccionar', compact('grados', 'secciones'));
    }

    /* =======================================================
     *  EXPORTAR ASISTENCIA — *MEJORADO*
     * ======================================================= */
    public function exportar(Request $request)
    {
        $formato   = $request->formato;
        $grados    = $request->grados ?? [];
        $secciones = $request->secciones ?? [];
        $fecha     = $request->input('fecha', date('Y-m-d')); // ← fecha agregada

        // ========== EXCEL MULTI-HOJA ==========
        if ($formato === 'excel') {
            return Excel::download(
                new AsistenciasExport($grados, $secciones, $fecha),
                'asistencias.xlsx'
            );
        }

        // ========== CSV ==========
        if ($formato === 'csv') {
            return Excel::download(
                new AsistenciasExport($grados, $secciones, $fecha),
                'asistencias.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        // ========== QUERY BASE ==========
        $query = Asistencia::with('alumno.grado', 'alumno.seccion')
            ->whereDate('fecha', $fecha)
            ->when($grados, fn($q)=>$q->whereHas('alumno', fn($a)=>$a->whereIn('grado_id',$grados)))
            ->when($secciones, fn($q)=>$q->whereHas('alumno', fn($a)=>$a->whereIn('seccion_id',$secciones)));

        // ========== PDF ==========
        if ($formato === 'pdf') {
            $asistencias = $query->get();
            $pdf = Pdf::loadView('asistencias.pdf', compact('asistencias'))
                     ->setPaper('a4', 'portrait');
            return $pdf->download('asistencias.pdf');
        }

        // ========== IMPRESORA ==========
        if ($formato === 'impresora') {
            $asistencias = $query->get();
            return view('asistencias.imprimir_directo', compact('asistencias'));
        }

        return back()->with('error', 'Formato no reconocido.');
    }

    /* =======================================================
     *  MARCAR ASISTENCIA
     * ======================================================= */
    public function marcar(Request $request, $grado_id, $seccion_id)
    {
        $fecha = Carbon::parse(
            $request->input('fecha', now()->toDateString())
        )->format('Y-m-d');

        $grado   = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);

        $alumnos = Alumno::with([
            'grado','seccion',
            'asistencias' => function($q) use ($fecha) {
                $q->whereDate('fecha', $fecha);
            }
        ])
        ->where('grado_id', $grado_id)
        ->where('seccion_id', $seccion_id)
        ->orderBy('nombre_apellido')
        ->get();

        return view('asistencias.marcar', compact('grado','seccion','alumnos','fecha'));
    }

    /* =======================================================
     *  IMPRESIÓN SIMPLE
     * ======================================================= */
    public function imprimir($grado_id, $seccion_id)
    {
        $alumnos = Alumno::with('grado','seccion')
            ->where('grado_id', $grado_id)
            ->where('seccion_id', $seccion_id)
            ->get();

        return view('asistencias.imprimir_directo', compact('alumnos'));
    }

    /* =======================================================
     *  VER SOLO (READONLY)
     * ======================================================= */
    public function verSolo(Request $request, $grado_id, $seccion_id)
    {
        $fecha = $request->input('fecha', date('Y-m-d'));

        $grado   = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);

        $alumnos = Alumno::with([
            'grado','seccion',
            'asistencias' => function($q) use ($fecha){
                $q->whereDate('fecha', $fecha);
            }
        ])
        ->where('grado_id', $grado_id)
        ->where('seccion_id', $seccion_id)
        ->orderBy('nombre_apellido')
        ->get();

        $soloVer = true;

        return view('asistencias.ver', compact(
            'grado','seccion','alumnos','fecha','soloVer'
        ));
    }

    /* =======================================================
     *  QR AUTOMÁTICO
     * ======================================================= */
    public function marcarPorQr(Request $request, string $token)
    {
        $alumno = Alumno::where('qr_token', $token)->firstOrFail();

        $ahora = Carbon::now();

        $corteStr = config('asistencia.corte', '13:00');
        [$h, $m] = explode(':', $corteStr);
        $corte = (clone $ahora)->setTime((int)$h, (int)$m);

        $estado = $ahora->lt($corte) ? 'ASISTIÓ' : 'TARDE';

        $asistencia = Asistencia::firstOrCreate(
            ['alumno_id' => $alumno->id, 'fecha' => $ahora->toDateString()],
            ['estado'   => $estado]
        );

        return response()->view('asistencias.ok', [
            'alumno' => $alumno,
            'estado' => $asistencia->estado,
            'hora'   => $ahora->format('H:i'),
        ]);
    }

    /* =======================================================
     *  QR — GENERAR Y MOSTRAR
     * ======================================================= */
    public function credenciales(Request $request)
    {
        $q = Alumno::with(['grado','seccion'])->orderBy('nombre_apellido');

        if (auth()->user()->role === 'auxiliar') {
            $permitidas = auth()->user()->secciones()->pluck('secciones.id')->all();
            $q->whereIn('seccion_id', $permitidas);
        }

        if ($request->filled('grado_id'))   $q->where('grado_id', $request->grado_id);
        if ($request->filled('seccion_id')) $q->where('seccion_id', $request->seccion_id);

        $alumnos   = $q->get();
        $grados    = Grado::all();
        $secciones = Seccion::all();

        return view('alumnos.qr', compact('alumnos','grados','secciones'));
    }

    public function generarQRFaltantes()
    {
        $creados = 0;

        Alumno::whereNull('qr_token')->chunkById(200, function($chunk) use (&$creados) {
            foreach ($chunk as $al) {
                do {
                    $al->qr_token = (string) Str::uuid();
                    try {
                        $al->save();
                        $ok = true;
                        $creados++;
                    } catch (QueryException $e) {
                        $ok = false;
                    }
                } while(!$ok);
            }
        });

        return back()->with('success', "QR creados para {$creados} alumno(s) sin token.");
    }

    public function emitirQR(Alumno $alumno)
    {
        $alumno->qr_token = (string) Str::uuid();
        $alumno->save();

        return back()->with('success', "QR emitido para {$alumno->nombre_apellido}.");
    }
}
