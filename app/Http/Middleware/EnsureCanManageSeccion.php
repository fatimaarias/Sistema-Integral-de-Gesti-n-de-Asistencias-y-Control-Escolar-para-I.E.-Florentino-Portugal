<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCanManageSeccion
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) abort(401);

        $seccionId = (int) $request->route('seccion'); // nombre del parámetro en tu ruta

        if ($user->role === 'auxiliar') {
            $ok = $user->secciones()->where('secciones.id', $seccionId)->exists();
            if (!$ok) abort(403, 'No autorizado para esta sección.');
        }

        return $next($request);
    }
}
