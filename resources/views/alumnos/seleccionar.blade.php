@extends('layouts.app')

@section('content')
@php
  // Si es auxiliar, lista de secciones permitidas (ids). Para admin/secundario es []
  $permitidas = auth()->user()->role === 'auxiliar'
      ? auth()->user()->secciones()->pluck('secciones.id')->toArray()
      : [];

  $colorClasses = ['course-blue','course-purple','course-yellow','course-red'];
  $cardIndex    = 0;
@endphp

<style>
    .page-bg-alumnos {
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 40%,#f9fafb 100%);
        min-height: calc(100vh - 80px);
        padding: 24px;
        border-radius: 32px;
    }

    .page-title {
        font-weight: 700;
        font-size: 1.6rem;
        color: #0f172a;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: .95rem;
    }

    .role-chip {
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:4px 12px;
        font-size:.8rem;
        font-weight:600;
        background:#eef2ff;
        color:#4f46e5;
    }

    /* ===== Tarjetas tipo "course cards" ===== */
    .course-card {
        border-radius: 26px;
        color:#f9fafb;
        padding: 18px 20px;
        border: none;
        box-shadow: 0 18px 40px rgba(15,23,42,0.25);
        display:flex;
        align-items:stretch;
        justify-content:space-between;
        gap:16px;
        position:relative;
        overflow:hidden;
        transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
    }

    .course-card::before{
        content:'';
        position:absolute;
        inset:-40%;
        background: radial-gradient(circle at 0 0, rgba(255,255,255,.28), transparent 55%);
        opacity:.8;
        pointer-events:none;
    }

    .course-card:hover{
        transform: translateY(-4px);
        filter:brightness(1.03);
        box-shadow:0 26px 55px rgba(15,23,42,0.35);
    }

    .course-main {
        position:relative;
        z-index:1;
        flex:1;
        min-width:0;
    }

    .course-title{
        font-weight:700;
        font-size:1rem;
        margin-bottom:4px;
    }

    .course-subtitle{
        font-size:.8rem;
        opacity:.9;
    }

    .course-divider{
        margin-top:10px;
        height:3px;
        width:65%;
        border-radius:999px;
        background:rgba(249,250,251,.75);
        opacity:.85;
    }

    .course-meta{
        position:relative;
        z-index:1;
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        justify-content:space-between;
        min-width:80px;
    }

    .course-badge-num{
        width:46px;
        height:46px;
        border-radius:999px;
        background:rgba(15,23,42,.08);
        border:1px solid rgba(249,250,251,.6);
        display:flex;
        align-items:center;
        justify-content:center;
        font-weight:700;
        font-size:1.1rem;
        margin-bottom:2px;
    }

    .course-badge-label{
        font-size:.7rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        opacity:.9;
    }

    .course-actions{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
        margin-top:14px;
    }

    .course-btn-main{
        border-radius:999px;
        border:none;
        padding:5px 14px;
        font-size:.8rem;
        font-weight:600;
        background:rgba(15,23,42,.12);
        color:#f9fafb;
        display:inline-flex;
        align-items:center;
        gap:6px;
        text-decoration:none;
        white-space:nowrap;
    }

    .course-btn-main:hover{
        background:rgba(15,23,42,.23);
        color:#fff;
    }

    .course-btn-light{
        border-radius:999px;
        border:1px solid rgba(249,250,251,.65);
        padding:4px 12px;
        font-size:.78rem;
        font-weight:600;
        background:transparent;
        color:#f9fafb;
        text-decoration:none;
        white-space:nowrap;
    }

    .course-btn-light:hover{
        background:rgba(249,250,251,.12);
        color:#fff;
    }

    .course-import{
        margin-top:10px;
        padding:8px 10px;
        border-radius:16px;
        background:rgba(15,23,42,.12);
        font-size:.75rem;
    }

    .course-import input[type="file"]{
        font-size:.75rem;
        border-radius:999px;
        padding:4px 10px;
        background:#f9fafb;
        border:none;
    }

    .course-import button{
        border-radius:999px;
        border:none;
        padding:4px 10px;
        font-size:.75rem;
        font-weight:600;
    }

    .course-import small{
        display:block;
        margin-top:4px;
        opacity:.85;
    }

    /* Paletas de color */
    .course-blue{
        background:linear-gradient(135deg,#47464a,#6366f1);
    }
    .course-purple{
        background:linear-gradient(135deg,#dbb4ff,#6366f1);
    }
    .course-yellow{
        background:linear-gradient(135deg,#dbb4ff,#6366f1);
    }
    .course-red{
        background:linear-gradient(135deg,#47464a,#6366f1);
    }

    .btn-back-soft{
        border-radius:999px;
        padding:8px 20px;
        font-weight:600;
        margin-top:8px;
    }

    @media (max-width:992px){
        .page-bg-alumnos{
            border-radius:0;
            padding:16px;
        }
    }
</style>

<div class="page-bg-alumnos">
    <div class="container-fluid">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
            <div>
                <h2 class="page-title">👨‍🏫 Añadir alumnos</h2>
                <p class="page-subtitle mb-0">
                    Selecciona el <strong>grado</strong> y la <strong>sección</strong> donde deseas registrar o importar alumnos.
                </p>
            </div>

            <div class="mt-3 mt-lg-0">
                <span class="role-chip">
                    <i class="bi bi-mortarboard"></i>
                    {{ ucfirst(auth()->user()->role ?? 'Usuario') }}
                </span>
            </div>
        </div>

        {{-- Grid de tarjetas: 2 por fila en desktop --}}
        <div class="row g-3">
            @foreach ($grados as $grado)
                @foreach ($secciones as $seccion)
                    {{-- Oculta tarjetas que el auxiliar no puede gestionar --}}
                    @if(auth()->user()->role !== 'auxiliar' || in_array($seccion->id, $permitidas))

                        @php
                            $colorClass = $colorClasses[$cardIndex % count($colorClasses)];
                            $cardIndex++;
                        @endphp

                        <div class="col-12 col-md-6">
                            <div class="course-card {{ $colorClass }}">
                                {{-- Texto principal --}}
                                <div class="course-main">
                                    <div class="course-title">
                                        {{ $grado->nombre }} {{ $seccion->nombre }} – Lista de alumnos
                                    </div>
                                    <div class="course-subtitle">
                                        Gestiona la asistencia y el registro de estudiantes de esta sección
                                        de forma rápida y organizada.
                                    </div>
                                    <div class="course-divider"></div>

                                    <div class="course-actions">
                                        <a href="{{ route('alumnos.ver', [$grado->id, $seccion->id]) }}"
                                           class="course-btn-main">
                                            👁️ Ver alumnos
                                        </a>

                                        <a href="{{ route('alumnos.create', ['grado_id' => $grado->id, 'seccion_id' => $seccion->id]) }}"
                                           class="course-btn-light">
                                            ➕ Añadir alumno
                                        </a>
                                    </div>

                                    <div class="course-import mt-2">
                                        <form action="{{ route('alumnos.importar') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="grado_id" value="{{ $grado->id }}">
                                            <input type="hidden" name="seccion_id" value="{{ $seccion->id }}">

                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <input type="file" name="archivo" class="form-control form-control-sm" required>
                                                <button class="btn btn-light btn-sm">
                                                    📥 Importar
                                                </button>
                                            </div>
                                            <small>Acepta archivos .xlsx o .csv</small>
                                        </form>
                                    </div>
                                </div>

                                {{-- Lado derecho tipo burbuja --}}
                                <div class="course-meta">
                                    <div class="text-end">
                                        <div class="course-badge-num">
                                            {{ $grado->nombre }}
                                        </div>
                                        <div class="course-badge-label">
                                            Sección {{ $seccion->nombre }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

     
    </div>
</div>
@endsection
