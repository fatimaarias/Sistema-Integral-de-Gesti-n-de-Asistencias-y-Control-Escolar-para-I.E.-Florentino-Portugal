@extends('layouts.app')

@section('content')
<style>
    .view-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 40%,#f9fafb 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .view-title{
        font-weight: 700;
        font-size: 1.5rem;
        color:#0f172a;
    }
    .view-subtitle{
        font-size: .9rem;
        color:#6b7280;
    }

    .card-glass{
        border-radius: 24px;
        border:none;
        background: rgba(255,255,255,.92);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        backdrop-filter: blur(10px);
    }

    .date-chip{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:6px 14px;
        border-radius:999px;
        background:#eef2ff;
        color:#4f46e5;
        font-size:.85rem;
        font-weight:600;
    }

    .date-input-rounded{
        border-radius:999px;
        border:1px solid #d1d5db;
        padding-inline:14px;
        font-size:.9rem;
    }

    .legend-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:4px 10px;
        border-radius:999px;
        font-size:.78rem;
        background:#f3f4f6;
        color:#4b5563;
        margin-right:6px;
        margin-bottom:6px;
    }
    .legend-dot{
        width:10px;
        height:10px;
        border-radius:999px;
    }

    .status-pill{
        border-radius:999px;
        font-size:.85rem;
        padding:6px 14px;
        font-weight:600;
    }
    .status-asistio{
        background:rgba(34,197,94,.12);
        color:#15803d;
    }
    .status-tarde{
        background:rgba(250,204,21,.16);
        color:#92400e;
    }
    .status-falto{
        background:rgba(239,68,68,.12);
        color:#b91c1c;
    }
    .status-justificada{
        background:rgba(56,189,248,.12);
        color:#0369a1;
    }
    .status-sin{
        background:#e5e7eb;
        color:#4b5563;
    }

    .table-soft thead{
        background:linear-gradient(90deg,#eef2ff,#e0f2fe);
        border-bottom:none;
    }
    .table-soft thead th{
        border-bottom:none !important;
        font-size:.8rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
    }
    .table-soft tbody tr{
        border-bottom:1px solid #f1f5f9;
        transition:background .12s ease, transform .08s ease;
    }
    .table-soft tbody tr:hover{
        background:#f9fafb;
        transform:translateY(-1px);
    }

    .btn-back-soft{
        border-radius:999px;
        padding-inline:18px;
    }
</style>

<div class="view-bg">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card card-glass p-4">
                {{-- Encabezado --}}
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3">
                    <div>
                        <h2 class="view-title">
                            Visualización de Asistencia — {{ $grado->nombre }} {{ $seccion->nombre }}
                        </h2>
                        <p class="view-subtitle mb-0">
                            Revisa el estado de asistencia de los alumnos para la fecha seleccionada.
                        </p>
                    </div>
                    
                    {{-- Fecha --}}
                    <div class="mt-3 mt-lg-0 d-flex flex-column align-items-lg-end gap-2">
                        <span class="date-chip">
                            <i class="bi bi-calendar-week"></i>
                            Día seleccionado: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                        </span>
                                        

                        <form method="GET"
                              action="{{ route('asistencias.ver', [$grado->id, $seccion->id]) }}"
                              class="d-flex gap-2">
                            <input type="date"
                                   name="fecha"
                                   value="{{ $fecha }}"
                                   class="form-control date-input-rounded"
                                   style="max-width: 220px;">
                        </form>
                    </div>
                </div>

                {{-- Leyenda --}}
                <div class="mb-3 d-flex flex-wrap">
                    <div class="legend-pill">
                        <span class="legend-dot" style="background:#22c55e;"></span>
                        Asistió
                    </div>
                    <div class="legend-pill">
                        <span class="legend-dot" style="background:#facc15;"></span>
                        Tarde
                    </div>
                    <div class="legend-pill">
                        <span class="legend-dot" style="background:#ef4444;"></span>
                        Faltó
                    </div>
                    <div class="legend-pill">
                        <span class="legend-dot" style="background:#38bdf8;"></span>
                        Justificada
                    </div>
                    <div class="legend-pill">
                        <span class="legend-dot" style="background:#9ca3af;"></span>
                        Sin registrar
                    </div>
                </div>

                @php
                    $map = [
                      'ASISTIÓ'     => ['class' => 'status-asistio',     'txt' => 'Asistió'],
                      'TARDE'       => ['class' => 'status-tarde',       'txt' => 'Tarde'],
                      'FALTÓ'       => ['class' => 'status-falto',       'txt' => 'Faltó'],
                      'JUSTIFICADA' => ['class' => 'status-justificada', 'txt' => 'Justificada'],
                    ];
                @endphp

                {{-- Tabla --}}
                <div class="table-responsive mt-2">
                    <table class="table table-soft align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:70px">#</th>
                                <th>Alumno</th>
                                <th style="width:220px" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alumnos as $al)
                                @php
                                    $estado = optional($al->asistencias->first())->estado;
                                    $config = $estado && isset($map[$estado]) ? $map[$estado] : ['class' => 'status-sin','txt' => 'Sin registrar'];
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width:32px;height:32px;background:#eef2ff;color:#4f46e5;font-size:.8rem;font-weight:700;">
                                                {{ strtoupper(substr($al->nombre_apellido,0,1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size:.93rem;">
                                                    {{ $al->nombre_apellido }}
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-pill {{ $config['class'] }}">
                                            {{ $config['txt'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            @if($alumnos->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No hay alumnos registrados en esta sección.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Botón volver --}}
                <div class="mt-4 d-flex justify-content-center">
                    <a href="{{ route('asistencias.seleccionar') }}"
                       class="btn btn-outline-secondary btn-back-soft">
                        ← Volver a selección de secciones
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Autoenviar al cambiar la fecha --}}
<script>
document.querySelector('input[name="fecha"]')
  ?.addEventListener('change', e => e.target.form.submit());
</script>
@endsection
