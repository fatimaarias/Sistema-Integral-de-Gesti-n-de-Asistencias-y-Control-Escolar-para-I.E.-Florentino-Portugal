@extends('layouts.app')

@section('content')
<style>
    .stats-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 35%,#f9fafb 100%);
        border-radius: 32px;
        padding: 24px;
        min-height: calc(100vh - 80px);
    }
    .stats-title{
        font-weight:700;
        font-size:1.6rem;
        color:#0f172a;
    }
    .stats-sub{
        font-size:.95rem;
        color:#6b7280;
    }
    .chip-salon{
        display:inline-flex;
        align-items:center;
        gap:8px;
        border-radius:999px;
        padding:6px 14px;
        font-size:.8rem;
        font-weight:600;
        background:#eef2ff;
        color:#4f46e5;
    }
    .card-soft{
        border-radius:22px;
        border:none;
        background:#ffffff;
        box-shadow:0 18px 40px rgba(148,163,184,.28);
    }
    .metric-pill{
        border-radius:14px;
        padding:10px 14px;
        background:#f9fafb;
    }
    .metric-label{
        font-size:.8rem;
        color:#6b7280;
        margin-bottom:2px;
    }
    .metric-value{
        font-weight:700;
        font-size:1.15rem;
        color:#111827;
    }
    .chart-box{
        position:relative;
        height:300px;
        width:100%;
    }
    @media(max-width:992px){
        .stats-bg{
            border-radius:0;
            padding:16px;
        }
        .chart-box{height:260px;}
    }
</style>

@php
    $salones      = $salones ?? collect();
    $grado_id     = $grado_id ?? null;
    $seccion_id   = $seccion_id ?? null;

    $resumenSalon = $resumenSalon ?? ['puntualidad'=>0,'tardanzas'=>0,'faltas'=>0,'total'=>0];
    $generoSalon  = $generoSalon  ?? ['masculino'=>0,'femenino'=>0];

    $conteoDias   = $conteoDias ?? [];
    $asistenciaMes = $asistenciaMes ?? [];
    $topTardes     = $topTardes ?? [];
@endphp

<div class="stats-bg">
    <div class="container-xxl">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
            <div>
                <h2 class="stats-title mb-1">
                    <i class="bi bi-bar-chart-fill me-2"></i>
                    Estadísticas de asistencia
                </h2>
                <p class="stats-sub mb-0">
                    Visualiza la puntualidad, tardanzas y faltas por salón, además de la distribución por género.
                </p>
            </div>

            {{-- Selector --}}
            <form method="GET" action="{{ route('estadisticas.index') }}"
                  class="mt-3 mt-lg-0 d-flex flex-wrap justify-content-end gap-2">

                <div class="chip-salon">
                    <i class="bi bi-building me-1"></i>
                    <span>Salón seleccionado:</span>
                    <strong>{{ $salonLabel }}</strong>
                </div>

                <select name="grado_id" class="form-select" onchange="this.form.submit()">
                    @foreach($gradosLista as $g)
                        <option value="{{ $g->id }}" {{ $g->id == $grado_id ? 'selected' : '' }}>
                            {{ $g->nombre }}
                        </option>
                    @endforeach
                </select>

                <select name="seccion_id" class="form-select form-select-sm" style="max-width:130px;">
                    @foreach($seccionesLista as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $seccion_id ? 'selected' : '' }}>
                            Sección {{ $s->nombre }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-repeat me-1"></i> Ver estadísticas
                </button>
            </form>
        </div>

        {{-- Métricas rápidas --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="metric-pill">
                    <div class="metric-label">Puntualidad</div>
                    <div class="metric-value">{{ $resumenSalon['puntualidad'] }}%</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="metric-pill">
                    <div class="metric-label">Tardanzas</div>
                    <div class="metric-value">{{ $resumenSalon['tardanzas'] }}%</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="metric-pill">
                    <div class="metric-label">Faltas</div>
                    <div class="metric-value">{{ $resumenSalon['faltas'] }}%</div>
                </div>
            </div>
        </div>

        {{-- FILA 1: Estados + Género --}}
        <div class="row g-4">

            <div class="col-12 col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-graph-up-arrow me-1"></i> Asistencia por estado ({{ $salonLabel }})</h5>
                        <div class="chart-box"><canvas id="chartEstados"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-people-fill me-1"></i> Distribución por género</h5>
                        <div class="chart-box"><canvas id="chartGenero"></canvas></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- FILA 2: Por día + Línea mensual --}}
        <div class="row g-4 mt-2">

            <div class="col-12 col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-calendar-week me-1"></i> Asistencia por día de la semana</h5>
                        <div class="chart-box"><canvas id="chartDias"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-activity me-1"></i> Tendencia mensual de asistencia</h5>
                        <div class="chart-box"><canvas id="chartMes"></canvas></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- FILA 3: Top tardanzas --}}
        <div class="row g-4 mt-2">
            <div class="col-12 col-lg-12">
                <div class="card card-soft h-100">
                    <div class="card-body">
                        <h5><i class="bi bi-clock-history me-1"></i> Top 10 alumnos con más tardanzas</h5>
                        <div class="chart-box"><canvas id="chartTopTardes"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const resumen   = @json($resumenSalon);
    const genero    = @json($generoSalon);
    const dias      = @json($conteoDias);
    const meses     = @json($asistenciaMes);
    const topTardes = @json($topTardes);

    /* ===============================
       GRAFICO 1: Estados (Barra)
    ================================*/
    new Chart(document.getElementById('chartEstados'), {
        type: 'bar',
        data: {
            labels: ['Puntualidad','Tardanzas','Faltas'],
            datasets: [{
                data: [
                    resumen.puntualidad,
                    resumen.tardanzas,
                    resumen.faltas,
                ],
                backgroundColor:[
                    'rgba(34,197,94,0.3)',
                    'rgba(234,179,8,0.3)',
                    'rgba(239,68,68,0.3)',
                ],
                borderColor:[
                    'rgba(34,197,94,1)',
                    'rgba(234,179,8,1)',
                    'rgba(239,68,68,1)',
                ],
                borderWidth:2,
                borderRadius:8
            }]
        },
        options:{
            responsive:true,
            plugins:{ legend:{display:false} },
            scales:{ y:{ beginAtZero:true, ticks:{ callback:v=>v+'%' } } }
        }
    });

    /* ===============================
       GRAFICO 2: Género (Donut)
    ================================*/
    new Chart(document.getElementById('chartGenero'), {
        type:'doughnut',
        data:{
            labels:['Masculino','Femenino'],
            datasets:[{
                data:[genero.masculino, genero.femenino],
                backgroundColor:['#3b82f6','#ec4899'],
                borderWidth:2,
                borderColor:'#fff'
            }]
        },
        options:{
            responsive:true,
            cutout:'60%',
            plugins:{ legend:{position:'bottom'} }
        }
    });

    /* ===============================
       GRAFICO 3: Asistencia por día
    ================================*/
    new Chart(document.getElementById('chartDias'), {
        type:'bar',
        data:{
            labels:['Lun','Mar','Mié','Jue','Vie'],
            datasets:[{
                label:'Asistencias',
                data: dias,
                backgroundColor:'rgba(99,102,241,0.35)',
                borderColor:'rgba(99,102,241,1)',
                borderWidth:2,
                borderRadius:8
            }]
        },
        options:{ responsive:true }
    });

    /* ===============================
       GRAFICO 4: Tendencia mensual
    ================================*/
    new Chart(document.getElementById('chartMes'), {
        type:'line',
        data:{
            labels:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            datasets:[{
                label:'% Asistencia',
                data: meses,
                fill:false,
                borderColor:'#10b981',
                tension:0.3,
                borderWidth:3
            }]
        },
        options:{
            responsive:true,
            scales:{ y:{ beginAtZero:true } }
        }
    });

    /* ===============================
       GRAFICO 5: Top 10 tardanzas
    ================================*/
    new Chart(document.getElementById('chartTopTardes'), {
        type:'bar',
        data:{
            labels: topTardes.map(t => t.nombre),
            datasets:[{
                label:'Tardanzas',
                data:  topTardes.map(t => t.total),
                backgroundColor:'rgba(239,68,68,0.45)',
                borderColor:'rgba(239,68,68,1)',
                borderWidth:2,
                borderRadius:6
            }]
        },
        options:{
            responsive:true,
            plugins:{
                legend:{display:false},
                tooltip:{ enabled:true }
            },
            scales:{ y:{ beginAtZero:true } }
        }
    });
</script>

@endsection
