@extends('layouts.app')

@section('content')
<style>
    .dash-bg {
        background: radial-gradient(circle at top left, #e9d5ff 0, #eff6ff 35%, #f9fafb 100%);
        min-height: calc(100vh - 80px);
        padding: 24px;
        border-radius: 32px;
    }

    .dash-title {
        font-weight: 700;
        font-size: 1.6rem;
        color: #0f172a;
    }

    .dash-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .dash-card {
        border-radius: 24px;
        border: none;
        backdrop-filter: blur(8px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 55px rgba(15, 23, 42, 0.16);
    }

    .chip-soft {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        background: #eef2ff;
        color: #4f46e5;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #111827;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.85rem;
    }

    .ranking-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
    }

    .ranking-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ranking-avatar {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        background: linear-gradient(135deg, #4f46e5, #a855f7);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .ranking-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #111827;
    }

    .ranking-meta {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .badge-section {
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 0.75rem;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
    }

    .ranking-score {
        font-weight: 700;
        font-size: 0.95rem;
        color: #16a34a;
    }

    /* Contenedor fijo para el gráfico */
    .chart-wrapper {
        position: relative;
        height: 260px;
        width: 100%;
    }

    /* Tarjetas de acciones rápidas */
    .quick-card {
        border-radius: 20px;
        border: none;
        background: #ffffff;
        box-shadow: 0 12px 30px rgba(148, 163, 184, 0.25);
        height: 100%;
    }

    .quick-card h5 {
        font-weight: 600;
    }

    /* Panel derecho tipo "resumen" */
    .profile-panel {
        border-radius: 28px;
        background: radial-gradient(circle at top, #5b21ff 0, #020617 55%);
        color: #f9fafb;
        box-shadow: 0 25px 60px rgba(15,23,42,0.45);
    }

    .profile-avatar {
        width: 96px;
        height: 96px;
        border-radius: 999px;
        background: radial-gradient(circle at 30% 20%, #a855f7, #4f46e5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
        font-weight: 700;
        color: #e5e7eb;
        box-shadow: 0 0 0 6px rgba(148,163,184,0.35);
    }

    /* Botón exportar arriba */
    .btn-export {
        border: none;
        border-radius: 999px;
        background: linear-gradient(120deg, #6366f1, #a855f7);
        color: #f9fafb;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 14px 35px rgba(79, 70, 229, 0.55);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        white-space: nowrap;
    }

    .btn-export i {
        font-size: 1rem;
    }

    .btn-export:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 20px 45px rgba(79, 70, 229, 0.75);
    }

    .btn-export:active {
        transform: translateY(0);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.6);
    }

    /* ====== CALENDARIO PANEL DERECHO ====== */
    .calendar-wrapper{
        border-radius:24px;
        background:radial-gradient(circle at 0 0,#312e81 0,#020617 70%);
        padding:16px 18px 18px;
        box-shadow:0 18px 40px rgba(15,23,42,0.85);
        border:1px solid rgba(129,140,248,0.45);
    }

    .calendar-header{
        color:#e5e7eb;
    }

    .calendar-month{
        color:#e5e7eb;
        font-size:.95rem;
        font-weight:600;
        letter-spacing:.04em;
    }

    .calendar-nav-btn{
        border:none;
        background:rgba(15,23,42,0.7);
        color:#e5e7eb;
        width:32px;
        height:32px;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:.9rem;
        transition:all .2s ease;
        box-shadow:0 8px 20px rgba(15,23,42,0.7);
    }
    .calendar-nav-btn:hover{
        background:linear-gradient(135deg,#6366f1,#a855f7);
    }

    .calendar-weekdays{
        display:grid;
        grid-template-columns:repeat(7,1fr);
        font-size:.7rem;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:rgba(209,213,219,0.85);
        margin-bottom:8px;
    }
    .calendar-weekdays span{
        text-align:center;
    }

    .calendar-grid{
        display:grid;
        grid-template-columns:repeat(7,1fr);
        gap:6px;
    }

    .calendar-day{
        width:100%;
        aspect-ratio:1/1;
        border-radius:16px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:.85rem;
        color:#e5e7eb;
        cursor:default;
        position:relative;
        background:rgba(15,23,42,0.75);
        transition:transform .12s ease, box-shadow .12s ease, background .12s ease;
    }
    .calendar-day span{
        position:relative;
        z-index:1;
    }

    .calendar-day:not(.empty):hover{
        transform:translateY(-2px) scale(1.03);
        box-shadow:0 10px 26px rgba(15,23,42,0.9);
        background:rgba(30,64,175,0.95);
    }

    .calendar-day.today{
        background:linear-gradient(135deg,#6366f1,#a855f7);
        box-shadow:0 12px 30px rgba(79,70,229,0.95);
        font-weight:700;
    }
    .calendar-day.today span{
        text-shadow:0 0 8px rgba(15,23,42,0.85);
    }

    .calendar-day.weekend span{
        color:#a5b4fc;
    }

    .calendar-day.empty{
        background:transparent;
        box-shadow:none;
        cursor:default;
    }

    .calendar-day::after{
        content:'';
        position:absolute;
        bottom:6px;
        left:50%;
        transform:translateX(-50%);
        width:5px;
        height:5px;
        border-radius:999px;
        background:transparent;
    }
    .calendar-day.has-dot::after{
        background:#facc15;
    }

    .calendar-legend{
        display:flex;
        justify-content:space-between;
        margin-top:10px;
        font-size:.75rem;
        color:rgba(209,213,219,0.85);
    }
    .calendar-legend-item{
        display:flex;
        align-items:center;
        gap:6px;
    }
    .legend-dot{
        width:9px;
        height:9px;
        border-radius:999px;
    }
    .legend-dot-today{
        background:#facc15;
    }
    .legend-dot-weekend{
        background:#a5b4fc;
    }

    /* Mini-stats bajo el calendario */
    .mini-stats{
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:8px 12px;
        margin-top:8px;
    }
    .mini-stat-item{
        background:rgba(15,23,42,0.78);
        border-radius:16px;
        padding:8px 10px;
        display:flex;
        flex-direction:column;
        align-items:flex-start;
    }
    .mini-stat-label{
        font-size:.72rem;
        opacity:.75;
    }
    .mini-stat-value{
        font-size:1rem;
        font-weight:600;
        color:#e5e7eb;
    }

    @media (max-width: 992px) {
        .dash-bg {
            border-radius: 0;
            padding: 16px;
        }
    }
</style>

@php
    // Valores por defecto para el gráfico (por si no llega nada del controlador)
    $labelsChart        = $labelsChart        ?? ['Lun','Mar','Mié','Jue','Vie'];
    $asistenciaPositiva = $asistenciaPositiva ?? [90,94,88,92,95];
    $tardanzasChart     = $tardanzasChart     ?? [10,8,12,9,7];

    $porcentajeAsistenciaGeneral = $porcentajeAsistenciaGeneral ?? 92;

    $ranking          = $rankingPuntualidad ?? [];
    $rankingSecciones = $rankingSecciones   ?? [];

    // Mini-stats rápidas usando los modelos (simple y directo para tu proyecto)
    $totalAlumnosDB    = \App\Models\Alumno::count();
    $totalGradosDB     = \App\Models\Grado::count();
    $totalSeccionesDB  = \App\Models\Seccion::count();

    // Para el botón de "Gestionar usuarios"
    $isAdmin = auth()->check() && (
        (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin')) ||
        (isset(auth()->user()->role) && auth()->user()->role === 'admin')
    );
@endphp

<div class="dash-bg">
    <div class="row g-4">
        {{-- Columna principal (gráfico + rankings + acciones) --}}
        <div class="col-12 col-xl-8">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
                <div>
                    <h2 class="dash-title">Hola, {{ Auth::user()->name ?? 'Administrador' }} 👋</h2>
                    <p class="dash-subtitle">
                        Este es el resumen general de la asistencia de la Institución Educativa Florentino Portugal.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    

                    <button type="button"
                            class="btn-export"
                            data-bs-toggle="modal"
                            data-bs-target="#modalFormatos">
                        <i class="bi bi-download"></i>
                        <span>Exportar asistencia</span>
                    </button>
                </div>
            </div>

            {{-- Gráfico + rankings --}}
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card dash-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Tendencia de asistencia</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    Porcentaje de asistencia registrada en los últimos días (presentes vs tardanzas).
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="stat-number">
                                    {{ $porcentajeAsistenciaGeneral }}%
                                </div>
                                <div class="stat-label">Asistencia general</div>
                            </div>
                        </div>

                        <div class="chart-wrapper">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Ranking alumnos --}}
                <div class="col-12">
                    <div class="card dash-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Alumnos más puntuales</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    Ranking según porcentaje de asistencias tempranas.
                                </p>
                            </div>
                            <span class="badge-section">Top 5</span>
                        </div>

                        @if(!empty($ranking) && count($ranking))
                            @foreach($ranking as $alumno)
                                <div class="ranking-item">
                                    <div class="ranking-left">
                                        <div class="ranking-avatar">
                                            {{ strtoupper(substr($alumno->nombre_apellido ?? $alumno->nombres ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="ranking-name">
                                                {{ $alumno->nombreCompleto ?? ($alumno->nombre_apellido ?? ($alumno->nombres ?? '')) }}
                                            </div>
                                            <div class="ranking-meta">
                                                {{ $alumno->grado_nombre ?? ($alumno->grado->nombre ?? '—') }}
                                                •
                                                {{ $alumno->seccion_nombre ?? ($alumno->seccion->nombre ?? '—') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="ranking-score">
                                            {{ number_format($alumno->porcentaje_puntualidad ?? 0, 1) }}%
                                        </div>
                                        <div class="ranking-meta">puntualidad</div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-1">
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted mb-0">
                                Aún no hay datos suficientes para mostrar el ranking de puntualidad.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Ranking de secciones --}}
                <div class="col-12">
                    <div class="card dash-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Secciones más puntuales</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                    Top 3 de grado y sección según porcentaje de asistencias tempranas (ASISTIÓ).
                                </p>
                            </div>
                            <span class="badge-section">Top 3</span>
                        </div>

                        @if(!empty($rankingSecciones) && count($rankingSecciones))
                            @foreach($rankingSecciones as $sec)
                                <div class="ranking-item">
                                    <div class="ranking-left">
                                        <div class="ranking-avatar">
                                            {{ ($sec->grado_nombre ?? '') . ($sec->seccion_nombre ?? '') }}
                                        </div>
                                        <div>
                                            <div class="ranking-name">
                                                {{ $sec->grado_nombre ?? '—' }} • {{ $sec->seccion_nombre ?? '—' }}
                                            </div>
                                            <div class="ranking-meta">
                                                {{ $sec->total_registros }} registros de asistencia
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="ranking-score">
                                            {{ number_format($sec->porcentaje_puntualidad ?? 0, 1) }}%
                                        </div>
                                        <div class="ranking-meta">puntualidad de la sección</div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-1">
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted mb-0">
                                Aún no hay datos suficientes para mostrar el ranking de secciones.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Acciones rápidas --}}
            <div class="row g-4">
                <div class="col-12 col-md-6 col-xl-3">
                    
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                   
                </div>

            
            </div>
        </div>

        {{-- Panel derecho con calendario --}}
        <div class="col-12 col-xl-4">
            <div class="profile-panel p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    
                    {{-- Calendario --}}
                    <div class="calendar-wrapper mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3 calendar-header">
                            <button class="calendar-nav-btn" id="calPrevBtn">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <div class="calendar-month" id="calendarMonthLabel">Mes Año</div>
                            <button class="calendar-nav-btn" id="calNextBtn">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <div class="calendar-weekdays">
                            <span>Lun</span>
                            <span>Mar</span>
                            <span>Mié</span>
                            <span>Jue</span>
                            <span>Vie</span>
                            <span>Sáb</span>
                            <span>Dom</span>
                        </div>

                        <div class="calendar-grid" id="calendarGrid">
                            {{-- Se rellena con JS --}}
                        </div>

                        <div class="calendar-legend mt-2">
                            <div class="calendar-legend-item">
                                <span class="legend-dot legend-dot-today"></span>
                                <span>Hoy</span>
                            </div>
                            <div class="calendar-legend-item">
                                <span class="legend-dot legend-dot-weekend"></span>
                                <span>Fin de semana</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mini resumen rápido --}}
                    <div class="mt-3">
                        <h6 class="text-uppercase mb-2" style="font-size:0.75rem; letter-spacing:.12em; opacity:.8;">
                            Resumen rápido
                        </h6>
                        <div class="mini-stats">
                            <div class="mini-stat-item">
                                <span class="mini-stat-label">Alumnos</span>
                                <span class="mini-stat-value">{{ $totalAlumnosDB }}</span>
                            </div>
                            <div class="mini-stat-item">
                                <span class="mini-stat-label">Grados</span>
                                <span class="mini-stat-value">{{ $totalGradosDB }}</span>
                            </div>
                            <div class="mini-stat-item">
                                <span class="mini-stat-label">Secciones</span>
                                <span class="mini-stat-value">{{ $totalSeccionesDB }}</span>
                            </div>
                            <div class="mini-stat-item">
                                <span class="mini-stat-label">Asistencia gral.</span>
                                <span class="mini-stat-value">{{ $porcentajeAsistenciaGeneral }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mt-4 mb-0" style="font-size:0.75rem; opacity:.75;">
                    Consejo: utiliza el menú izquierdo para cambiar de módulo y este panel para tener a la vista
                    tu calendario y el resumen diario de la asistencia.
                </p>
            </div>
        </div>
    </div>
</div>

@includeIf('asistencias.modales')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('attendanceChart');
            if (!ctx) return;

            const labels    = @json($labelsChart ?? []);
            const dataPos   = @json($asistenciaPositiva ?? []);
            const dataTarde = @json($tardanzasChart ?? []);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Asistencia positiva (%)',
                            data: dataPos,
                            fill: true,
                            tension: 0.4,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(129, 140, 248, 0.20)',
                            pointRadius: 4,
                            pointBackgroundColor: '#4f46e5'
                        },
                        {
                            label: 'Tardanzas (%)',
                            data: dataTarde,
                            fill: false,
                            tension: 0.4,
                            borderColor: '#f97316',
                            borderDash: [6, 4],
                            pointRadius: 4,
                            pointBackgroundColor: '#ea580c'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: { stepSize: 20 }
                        }
                    }
                }
            });
        });

        // ===== Calendario panel derecho =====
        (function initDashboardCalendar() {
            const monthLabel = document.getElementById('calendarMonthLabel');
            const grid       = document.getElementById('calendarGrid');
            const btnPrev    = document.getElementById('calPrevBtn');
            const btnNext    = document.getElementById('calNextBtn');

            if (!monthLabel || !grid) return;

            const today = new Date();
            let currentYear  = today.getFullYear();
            let currentMonth = today.getMonth(); // 0-11

            const monthNames = [
                'Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'
            ];

            function render() {
                const year  = currentYear;
                const month = currentMonth;

                const firstDay     = new Date(year, month, 1);
                const daysInMonth  = new Date(year, month + 1, 0).getDate();
                let startOffset    = firstDay.getDay() - 1; // queremos empezar en Lunes

                if (startOffset < 0) startOffset = 6; // si es domingo

                monthLabel.textContent = `${monthNames[month]} ${year}`;
                grid.innerHTML = '';

                // celdas vacías antes del 1
                for (let i = 0; i < startOffset; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.className = 'calendar-day empty';
                    grid.appendChild(emptyCell);
                }

                // días del mes
                for (let day = 1; day <= daysInMonth; day++) {
                    const cell = document.createElement('div');
                    cell.className = 'calendar-day';

                    const span = document.createElement('span');
                    span.textContent = day;
                    cell.appendChild(span);

                    const dow = new Date(year, month, day).getDay(); // 0 = dom, 6 = sáb
                    if (dow === 0 || dow === 6) {
                        cell.classList.add('weekend');
                    }

                    if (
                        day   === today.getDate() &&
                        month === today.getMonth() &&
                        year  === today.getFullYear()
                    ) {
                        cell.classList.add('today', 'has-dot');
                    }

                    grid.appendChild(cell);
                }
            }

            btnPrev?.addEventListener('click', function () {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                render();
            });

            btnNext?.addEventListener('click', function () {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                render();
            });

            render();
        })();
    </script>
@endpush
