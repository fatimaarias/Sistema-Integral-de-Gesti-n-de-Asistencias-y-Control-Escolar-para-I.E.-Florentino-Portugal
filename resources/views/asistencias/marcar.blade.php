@extends('layouts.app')

@section('content')
<style>
    .mark-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 35%,#f9fafb 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .mark-title{
        font-weight:700;
        font-size:1.6rem;
        color:#0f172a;
    }
    .mark-subtitle{
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

    .card-mark{
        border-radius:22px;
        border:none;
        background:#ffffff;
        box-shadow:0 18px 40px rgba(148,163,184,.35);
    }

    thead.mark-head{
        background:linear-gradient(90deg,#e0f2fe,#eef2ff);
    }

    .estado-label{
        font-size:.8rem;
        color:#6b7280;
        margin-bottom:0;
    }

    .estado-dot{
        display:inline-block;
        width:10px;
        height:10px;
        border-radius:999px;
        margin-right:6px;
    }

    .dot-presente{ background:#22c55e; }
    .dot-tarde{ background:#fbbf24; }
    .dot-falto{ background:#ef4444; }
    .dot-justificada{ background:#0ea5e9; }

    .radio-cell input[type="radio"]{
        width:18px;
        height:18px;
        cursor:pointer;
    }

    .justify-box textarea{
        font-size:.85rem;
    }

    .btn-save{
        border-radius:999px;
        padding-inline:22px;
        font-weight:600;
        box-shadow:0 14px 38px rgba(34,197,94,.55);
    }
    .btn-back{
        border-radius:999px;
        padding-inline:20px;
        font-weight:500;
    }

    /* Tarjeta de fecha */
    .date-card{
        border-radius:20px;
        border:none;
        background:#ffffff;
        box-shadow:0 14px 32px rgba(148,163,184,.4);
        padding:16px 20px;
    }

    .date-title{
        font-size:.9rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.12em;
        color:#6b7280;
        margin-bottom:4px;
    }


       .date-label{
        color:#4b5563;          /* gris fuerte */
        font-weight:600;
        font-size:.85rem;
        white-space:nowrap;
        display:flex;
        align-items:center;
        gap:6px;
    }

    .date-current{
        font-size:1.05rem;
        font-weight:600;
        color:#111827;
    }

    .date-help{
        font-size:.8rem;
        color:#9ca3af;
    }

    @media (max-width: 992px){
        .mark-bg{
            border-radius:0;
            padding:16px;
        }
    }
</style>

<div class="mark-bg">
    <div class="container-xxl">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
            <div>
                <h2 class="mark-title mb-1">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Marcar Asistencia — {{ $grado->nombre }} {{ $seccion->nombre }}
                </h2>
                <p class="mark-subtitle mb-0">
                    Selecciona el estado de asistencia para cada alumno en la fecha indicada.
                </p>
            </div>

            <div class="d-flex flex-column align-items-lg-end align-items-start gap-2 mt-3 mt-lg-0">
                <span class="chip-soft">
                    <i class="bi bi-calendar-event"></i>
                    <span>Hoy: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                </span>
                <small class="text-muted" style="font-size:.78rem;">
                    Puedes cambiar la fecha de registro en el panel de abajo.
                </small>
            </div>
        </div>

        {{-- Tarjeta visible para cambiar fecha --}}
        <div class="date-card mb-4">
            <form method="GET"
                  action="{{ route('asistencias.marcar', ['grado' => $grado->id, 'seccion' => $seccion->id]) }}"
                  class="row g-3 align-items-center">
                <div class="col-12 col-md-7">
                    <div class="date-title">
                        FECHA DE ASISTENCIA
                    </div>
                    <div class="date-current">
                        {{ \Carbon\Carbon::parse($fecha)->translatedFormat('d \\d\\e F \\d\\e Y') }}
                    </div>
                    <div class="date-help mt-1">
                        Selecciona una fecha distinta para ver o registrar asistencias de otro día.
                    </div>
                </div>

                <div class="col-12 col-md-5 d-flex align-items-center justify-content-md-end gap-2">
                    <label for="fecha" class="date-label mb-0">
                        <i class="bi bi-calendar-date"></i>
                        <span>Cambiar fecha</span>
                    </label>
                    <input type="date"
                           id="fecha"
                           name="fecha"
                           value="{{ $fecha }}"
                           max="{{ \Carbon\Carbon::now()->toDateString() }}"
                           class="form-control"
                           style="max-width: 220px"
                           onchange="this.form.submit()">
                </div>

                <noscript>
                    <div class="col-12 mt-2">
                        <button class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-repeat"></i> Ver
                        </button>
                    </div>
                </noscript>
            </form>
        </div>

        {{-- Leyenda de estados --}}
        <div class="mb-3 d-flex flex-wrap gap-3 justify-content-center">
            <p class="estado-label mb-0">
                <span class="estado-dot dot-presente"></span> Presente
            </p>
            <p class="estado-label mb-0">
                <span class="estado-dot dot-tarde"></span> Tarde
            </p>
            <p class="estado-label mb-0">
                <span class="estado-dot dot-falto"></span> Faltó
            </p>
            <p class="estado-label mb-0">
                <span class="estado-dot dot-justificada"></span> Justificada (requiere explicación)
            </p>
        </div>

        {{-- Formulario principal de guardado --}}
        <div class="card card-mark">
            <div class="card-body">
                <form action="{{ route('asistencias.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fecha" value="{{ $fecha }}">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="mark-head">
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th class="text-start">Nombre del Alumno</th>
                                    <th style="width:90px" class="text-center">Presente</th>
                                    <th style="width:90px" class="text-center">Tarde</th>
                                    <th style="width:90px" class="text-center">Faltó</th>
                                    <th style="width:210px" class="text-center">Justificada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($alumnos as $al)
                                    @php
                                        $registro      = $al->asistencias->first();
                                        $estadoActual  = $registro->estado ?? '';
                                        $justificacion = $registro->justificacion ?? '';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-start fw-semibold">
                                            {{ $al->nombre_apellido }}
                                        </td>

                                        {{-- Presente --}}
                                        <td class="text-center radio-cell">
                                            <input type="radio"
                                                   name="estado[{{ $al->id }}]"
                                                   value="ASISTIÓ"
                                                   {{ $estadoActual === 'ASISTIÓ' ? 'checked' : '' }}>
                                        </td>

                                        {{-- Tarde --}}
                                        <td class="text-center radio-cell">
                                            <input type="radio"
                                                   name="estado[{{ $al->id }}]"
                                                   value="TARDE"
                                                   {{ $estadoActual === 'TARDE' ? 'checked' : '' }}>
                                        </td>

                                        {{-- Faltó --}}
                                        <td class="text-center radio-cell">
                                            <input type="radio"
                                                   name="estado[{{ $al->id }}]"
                                                   value="FALTÓ"
                                                   {{ $estadoActual === 'FALTÓ' ? 'checked' : '' }}>
                                        </td>

                                        {{-- Justificada --}}
                                        <td class="text-center">
                                            <div class="radio-cell mb-1">
                                                <input type="radio"
                                                       name="estado[{{ $al->id }}]"
                                                       value="JUSTIFICADA"
                                                       class="radio-justificada"
                                                       data-id="{{ $al->id }}"
                                                       {{ $estadoActual === 'JUSTIFICADA' ? 'checked' : '' }}>
                                            </div>

                                            {{-- Contenedor de justificación --}}
                                            <div class="justify-box mt-1" id="justify-{{ $al->id }}" style="display: none;">
                                                <textarea name="justificacion[{{ $al->id }}]"
                                                          class="form-control"
                                                          rows="2"
                                                          placeholder="Escribe la justificación...">{{ $justificacion }}</textarea>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-save">
                            <i class="bi bi-save2 me-1"></i>
                            Guardar asistencias
                        </button>
                        <a href="{{ route('asistencias.agregarSeleccionar') }}" class="btn btn-outline-secondary btn-back ms-2">
                            <i class="bi bi-arrow-left-circle me-1"></i>
                            Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Script para mostrar/ocultar el campo de justificación --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const radios = document.querySelectorAll(".radio-justificada");

    radios.forEach(radio => {
        const id  = radio.dataset.id;
        const box = document.getElementById(`justify-${id}`);

        // Mostrar si ya viene seleccionada
        if (radio.checked) box.style.display = "block";

        // Escuchar todos los radios del alumno
        document.querySelectorAll(`input[name='estado[${id}]']`).forEach(r => {
            r.addEventListener("change", function() {
                if (this.value === "JUSTIFICADA") {
                    box.style.display = "block";
                } else {
                    box.style.display = "none";
                }
            });
        });
    });
});
</script>
@endsection
