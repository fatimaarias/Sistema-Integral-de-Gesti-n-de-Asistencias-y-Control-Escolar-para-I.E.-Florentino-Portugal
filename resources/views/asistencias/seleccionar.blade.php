@extends('layouts.app')

@section('content')
<style>
    .select-bg{
        background: radial-gradient(circle at top left,#7482ff 0,#ffffff 40%,#f9f9fb 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .select-title{
        font-weight: 700;
        font-size: 1.6rem;
        color:#0f172a;
    }
    .select-subtitle{
        font-size:.9rem;
        color:#6b7280;
    }

    .card-glass{
        border-radius:24px;
        border:none;
        background:rgba(255,255,255,.94);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        backdrop-filter: blur(10px);
    }

    .grade-card{
        border-radius:22px;
        border:none;
        padding:18px 18px 16px;
        background:linear-gradient(135deg,#7977ff,#7e77eb);
        color:#f9fafb;
        position:relative;
        overflow:hidden;
        box-shadow:0 16px 40px rgba(79,70,229,.35);
        transition:transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }
    .grade-card:nth-child(2n){
        background:linear-gradient(135deg,#ec4899,#f97316);
        box-shadow:0 16px 40px rgba(236,72,153,.35);
    }
    .grade-card:nth-child(3n){
        background:linear-gradient(135deg,#22c55e,#14b8a6);
        box-shadow:0 16px 40px rgba(34,197,94,.35);
    }

    .grade-card:hover{
        transform:translateY(-3px);
        filter:brightness(1.03);
        box-shadow:0 22px 55px rgba(15,23,42,.45);
    }

    .grade-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:4px 10px;
        border-radius:999px;
        background:rgba(15,23,42,.18);
        font-size:.78rem;
        font-weight:500;
    }

    .grade-title{
        font-size:1.1rem;
        font-weight:700;
        margin-bottom:2px;
    }
    .grade-sub{
        font-size:.82rem;
        opacity:.9;
    }

    .badge-num{
        position:absolute;
        top:14px;
        right:14px;
        border-radius:999px;
        background:rgba(15,23,42,.22);
        padding:6px 10px;
        font-size:.8rem;
        font-weight:600;
    }

    .btn-ghost-light{
        border-radius:999px;
        border:1px solid rgba(248,250,252,.7);
        background:rgba(15,23,42,.05);
        color:#f9fafb;
        font-size:.85rem;
        font-weight:600;
        padding:6px 14px;
        width:100%;
        margin-top:10px;
        text-decoration:none;
        display:inline-flex;
        justify-content:center;
        align-items:center;
        gap:6px;
        transition:background .12s, transform .12s;
    }
    .btn-ghost-light:hover{
        background:rgba(15,23,42,.18);
        transform:translateY(-1px);
        color:#f9fafb;
    }

    .btn-back-soft{
        border-radius:999px;
        padding-inline:18px;
    }
</style>

<div class="select-bg">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
                <div class="text-center mb-4">
                    <h2 class="select-title">📘 Visualizar asistencia</h2>
                    <p class="select-subtitle">
                        Elige un <strong>grado</strong> y <strong>sección</strong> para ver la asistencia de sus alumnos.
                    </p>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @foreach ($grados as $grado)
                        @foreach ($secciones as $seccion)
                            <div class="col">
                                <div class="grade-card h-100">
                                    <span class="badge-num">
                                        {{ $grado->nombre }} {{ $seccion->nombre }}
                                    </span>

                                    <div class="mb-2">
                                        <div class="grade-title">
                                            {{ $grado->nombre }} {{ $seccion->nombre }}
                                        </div>
                                        <div class="grade-sub">
                                            Visualiza la asistencia registrada de este grupo.
                                        </div>
                                    </div>

                                    <div class="grade-pill mt-1">
                                        <i class="bi bi-people"></i>
                                        Sección regular
                                    </div>

                                    <a href="{{ route('asistencias.verSolo', ['grado' => $grado->id, 'seccion' => $seccion->id]) }}"
                                       class="btn-ghost-light">
                                        <i class="bi bi-eye"></i>
                                        Ver asistencia
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                
            </div>
        </div>
    
</div>
@endsection
