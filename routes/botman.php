<?php

use BotMan\BotMan\BotMan;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Asistencia;
use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Seccion;

/** @var BotMan $botman */
$botman = resolve('botman');

/** Normaliza: minúsculas + sin acentos + espacios compactados */
$norm = function (string $t): string {
    $t = Str::lower($t);
    $t = strtr($t, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n'
    ]);
    return preg_replace('/\s+/', ' ', trim($t));
};

/** Extrae grado/seccion si aparece algo como "1 a", "1° a", "3 b" */
$parseGradoSeccion = function (string $q): array {
    if (preg_match('/\b(\d{1,2})\s*(?:°|o)?\s*([ab])\b/u', $q, $m)) {
        return [(int)$m[1], strtoupper($m[2])];
    }
    return [null, null];
};

/** Extrae fecha yyyy-mm-dd si viene, si no hoy */
$parseFecha = function (string $q): string {
    if (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $q, $m)) {
        return $m[1];
    }
    return Carbon::today()->toDateString();
};

$botman->hears('.*', function (BotMan $bot) use ($norm, $parseGradoSeccion, $parseFecha) {
    $raw = $bot->getMessage()->getText() ?? '';
    $q   = $norm($raw);

    // 1) ¿Quiénes faltaron hoy? / ¿Quiénes no vinieron? (+ opcional grado/sección/fecha)
    if (preg_match('/\b(quien|quienes)\b.*\b(faltaron|ausentes|no vinieron|no asistieron)\b/', $q)) {
        [$g, $s] = $parseGradoSeccion($q);
        $fecha   = $parseFecha($q);

        $asist = Asistencia::with('alumno')
            ->whereDate('fecha', $fecha)
            ->where('estado', 'FALTÓ');

        if ($g && $s) {
            $asist->whereHas('alumno', function ($qq) use ($g, $s) {
                $qq->whereHas('grado', fn($x) => $x->where('nombre', $g))
                   ->whereHas('seccion', fn($x) => $x->where('nombre', $s));
            });
        }

        $nombres = $asist->get()
            ->pluck('alumno.nombre_apellido')
            ->filter()
            ->values();

        if ($nombres->isEmpty()) {
            $bot->reply($g && $s
                ? "En {$g}° {$s} no hay faltas registradas en {$fecha} ✅"
                : "No hay faltas registradas en {$fecha} ✅");
        } else {
            $bot->reply(($g && $s ? "Faltaron en {$g}° {$s}: " : "Faltaron: ") . $nombres->implode(', '));
        }
        return;
    }

    // 2) ¿Quién llegó tarde en 1° A (hoy/fecha)?
    if (preg_match('/\b(llego|llegaron)\s+tarde\b/', $q)) {
        [$g, $s] = $parseGradoSeccion($q);
        if (!$g || !$s) {
            $bot->reply('¿En qué grado y sección? (ej: "¿Quién llegó tarde en 1° A?")');
            return;
        }
        $fecha = $parseFecha($q);

        $tardes = Asistencia::with('alumno')
            ->whereDate('fecha', $fecha)
            ->where('estado', 'TARDE')
            ->whereHas('alumno', function ($qq) use ($g, $s) {
                $qq->whereHas('grado', fn($x) => $x->where('nombre', $g))
                   ->whereHas('seccion', fn($x) => $x->where('nombre', $s));
            })
            ->get()
            ->pluck('alumno.nombre_apellido');

        if ($tardes->isEmpty()) {
            $bot->reply("En {$g}° {$s} no hay llegadas tarde en {$fecha} ✅");
        } else {
            $bot->reply("Llegaron tarde en {$g}° {$s}: " . $tardes->implode(', '));
        }
        return;
    }

    // 3) Estado de "Nombre de Alumno" (hoy/fecha)
    if (preg_match('/\bestado de\b/', $q)) {
        if (preg_match('/estado de\s+"([^"]+)"/u', $q, $m) || preg_match('/estado de\s+([a-záéíóúñ ]+)/u', $q, $m)) {
            $nombre = trim($m[1] ?? $m[0]);
            $fecha  = $parseFecha($q);

            $alumno = Alumno::whereRaw('LOWER(REPLACE(nombre_apellido,"ñ","n")) = ?', [
                strtr(Str::lower($nombre), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n'])
            ])->first();

            if (!$alumno) {
                $bot->reply("No encontré a **{$nombre}**.");
                return;
            }

            $asis = $alumno->asistencias()->whereDate('fecha', $fecha)->first();
            $bot->reply($asis
                ? "{$alumno->nombre_apellido} en {$fecha}: {$asis->estado}"
                : "{$alumno->nombre_apellido} no tiene registro en {$fecha}.");
            return;
        }
    }

    // 4) ¿Faltaron el 2025-11-10?
    if (preg_match('/\bfaltaron el\b/', $q)) {
        $fecha = $parseFecha($q);
        $nombres = Asistencia::with('alumno')
            ->whereDate('fecha', $fecha)
            ->where('estado','FALTÓ')
            ->get()
            ->pluck('alumno.nombre_apellido');

        $bot->reply($nombres->isEmpty()
            ? "No hay faltas registradas en {$fecha} ✅"
            : "Faltaron en {$fecha}: " . $nombres->implode(', ')
        );
        return;
    }

    // Respuesta por defecto
    $bot->reply("Puedo responder, por ejemplo:\n• ¿Quiénes faltaron hoy?\n• ¿Quién llegó tarde en 1° A?\n• Estado de \"Juan Pérez\" hoy\n• ¿Faltaron el 2025-11-10?");
});
