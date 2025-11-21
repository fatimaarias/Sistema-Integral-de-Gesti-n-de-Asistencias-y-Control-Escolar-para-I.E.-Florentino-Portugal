@extends('layouts.app')

@section('content')

@php
    $gradoNombre   = request('grado_nombre');
    $seccionNombre = request('seccion_nombre');
@endphp

<style>
    .create-bg {
        background: radial-gradient(circle at top left,#e0f2fe 0,#f5f3ff 40%,#dac3fe 100%);
        min-height: calc(100vh - 80px);
        padding: 32px 16px;
        border-radius: 32px;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .student-card {
        max-width: 720px;
        width: 100%;
        border-radius: 28px;
        border: none;
        background: #ffffff;
        box-shadow: 0 20px 45px rgba(148,163,184,0.35);
        padding: 32px 28px;
        position: relative;
        overflow: hidden;
    }

    .student-card::before{
        content:'';
        position:absolute;
        inset:-40%;
        background: radial-gradient(circle at 0 0,rgba(129,140,248,0.25),transparent 60%);
        opacity:.7;
        pointer-events:none;
    }

    .student-header {
        position:relative;
        z-index:1;
        text-align:center;
        margin-bottom:22px;
    }

    .student-avatar {
        width:70px; height:70px;
        border-radius:999px;
        background:linear-gradient(135deg,#38bdf8,#a855f7);
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto 10px;
        box-shadow:0 10px 25px rgba(129,140,248,0.7);
        color:#fff;
        font-size:2rem;
    }

    .student-title {
        font-weight:700;
        font-size:1.4rem;
        color:#0f172a;
        margin-bottom:4px;
    }

    .student-subtitle {
        font-size:.9rem;
        color:#6b7280;
    }

    .grade-chip {
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:4px 12px;
        background:#eef2ff;
        color:#4f46e5;
        font-size:.8rem;
        font-weight:600;
        margin-top:10px;
    }

    .form-label {
        font-weight:600;
        font-size:.9rem;
        color:#374151;
    }

    .form-control,
    .form-select {
        border-radius:14px;
        border:1px solid #e5e7eb;
        padding:9px 12px;
        font-size:.9rem;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#6366f1;
        box-shadow:0 0 0 3px rgba(129,140,248,0.35);
    }

    .btn-save {
        border-radius:999px;
        padding:9px 26px;
        font-weight:700;
        border:none;
        background:linear-gradient(135deg,#22c55e,#4ade80);
        color:#f9fafb;
        box-shadow:0 14px 30px rgba(34,197,94,0.45);
    }

    .btn-save:hover{
        filter:brightness(1.05);
    }

    .btn-cancel {
        border-radius:999px;
        padding:9px 22px;
        font-weight:600;
    }

    @media (max-width:768px){
        .student-card{
            padding:24px 18px;
            border-radius:22px;
        }
        .create-bg{
            border-radius:0;
            padding:20px 10px;
        }
    }
</style>

<div class="create-bg">
    <div class="student-card bg-white">
        <div class="student-header">
            <div class="student-avatar">
                <span>👧</span>
            </div>
            <h2 class="student-title">Registrar nuevo alumno</h2>
            <p class="student-subtitle mb-1">
                Completa los datos para añadirlo a la lista de la sección.
            </p>

            @if($gradoNombre && $seccionNombre)
                <div class="grade-chip">
                    <i class="bi bi-book"></i>
                    {{ $gradoNombre }}°  •  Sección {{ $seccionNombre }}
                </div>
            @endif
        </div>

        <form action="{{ route('alumnos.store') }}" method="POST" class="position-relative" style="z-index:1;">
            @csrf

            {{-- Campos invisibles necesarios --}}
            <input type="hidden" name="grado_id" value="{{ request('grado_id') }}">
            <input type="hidden" name="seccion_id" value="{{ request('seccion_id') }}">
            <input type="hidden" name="grado_nombre" value="{{ request('grado_nombre') }}">
            <input type="hidden" name="seccion_nombre" value="{{ request('seccion_nombre') }}">

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label">Nombre completo</label>
                    <input type="text"
                           name="nombres"
                           class="form-control"
                           placeholder="Ej: Jimmy Amorin"
                           required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text"
                           name="direccion"
                           class="form-control"
                           placeholder="Ej: Av. Principal 123">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Género</label>
                    <select name="genero" class="form-select" required>
                        <option value="">Seleccione género</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-save">
                    💾 Guardar alumno
                </button>
                <a href="{{ route('alumnos.seleccionar') }}" class="btn btn-secondary btn-cancel ms-2">
                    ⬅️ Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
