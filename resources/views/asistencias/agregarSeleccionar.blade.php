@extends('layouts.app')

@section('content')
<style>
    .select-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#effdff 35%,#99c6f3 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .select-title{
        font-weight:700;
        font-size:1.6rem;
        color:#0f172a;
    }
    .select-subtitle{
        font-size:.95rem;
        color:#6b7280;
    }

    .chip-soft{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:6px 12px;
        font-size:.8rem;
        font-weight:600;
        background:#eef2ff;
        color:#4f46e5;
    }

    .class-card{
        border-radius:22px;
        border:none;
        background:linear-gradient(135deg,#edeeff,#7696f4);
        box-shadow:0 14px 32px rgba(148,163,184,.35);
        padding:18px 18px 16px;
        transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .class-card:hover{
        transform:translateY(-3px);
        box-shadow:0 18px 40px rgba(148,163,184,.55);
        background:linear-gradient(135deg,#eef2ff,#ffffff);
    }

    .class-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:4px 10px;
        font-size:.78rem;
        background:#e0f2fe;
        color:#0369a1;
        font-weight:600;
    }

    .class-icon{
        width:38px;
        height:38px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(34,197,94,.08);
        color:#59278e;
        font-size:1.2rem;
    }

    .btn-mark{
        border-radius:999px;
        font-weight:600;
        font-size:.9rem;
        padding:8px 14px;
        box-shadow:0 12px 30px rgba(232, 229, 240, 0.45);
    }
    .btn-mark i{
        font-size:1rem;
    }

    .btn-back-soft{
        border-radius:999px;
        padding-inline:20px;
        font-weight:500;
    }

    @media (max-width: 992px){
        .select-bg{
            border-radius:0;
            padding:16px;
        }
    }
</style>

<div class="select-bg">
    <div class="container-xxl">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
            <div>
                <h2 class="select-title mb-1">🟩 Marcar Asistencia</h2>
                <p class="select-subtitle mb-0">
                    Elige un grado y sección para añadir o registrar asistencias del día.
                </p>
            </div>

            <div class="d-flex flex-column align-items-lg-end align-items-start gap-2 mt-3 mt-lg-0">
                <span class="chip-soft">
                    <i class="bi bi-calendar-week"></i>
                    <span>Hoy: {{ now()->format('d/m/Y') }}</span>
                </span>
                <small class="text-muted" style="font-size:.78rem;">
                    Se marcará la asistencia para la fecha: <strong>{{ request('fecha', now()->toDateString()) }}</strong>
                </small>
            </div>
        </div>

        {{-- Grid de tarjetas (grados x secciones) --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($grados as $grado)
                @foreach ($secciones as $seccion)
                    <div class="col">
                        <div class="class-card h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="class-icon">
                                    🏫
                                </div>
                                <span class="class-pill">
                                    {{ $grado->nombre }}° • {{ $seccion->nombre }}
                                </span>
                            </div>

                            <h4 class="fw-semibold mb-1 text-success" style="font-size:1.05rem;">
                                {{ $grado->nombre }}° {{ $seccion->nombre }}
                            </h4>
                            <p class="text-muted mb-3" style="font-size:.88rem;">
                                Registrar asistencia de este grupo en la fecha seleccionada.
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size:.78rem;">
                                    Sección regular • Turno mañana/tarde
                                </small>

                                {{-- Enlace para marcar asistencia --}}
                                <a href="{{ route('asistencias.marcar', [
                                        'grado'   => $grado->id,
                                        'seccion' => $seccion->id,
                                        'fecha'   => request('fecha', now()->toDateString())
                                    ]) }}"
                                   class="btn btn-success btn-mark text-white">
                                    <i class="bi bi-check2-square"></i>
                                    <span>Marcar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

    </div>
</div>
@endsection
