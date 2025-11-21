@extends('layouts.app')

@section('content')
<style>
    .usr-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 40%,#f9fafb 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .usr-title{
        font-weight:700;
        font-size:1.6rem;
        color:#0f172a;
    }
    .usr-subtitle{
        font-size:.95rem;
        color:#6b7280;
    }

    .usr-grid{
        display:grid;
        gap:20px;
        grid-template-columns:repeat(1,minmax(0,1fr));
    }
    @media (min-width:768px){
        .usr-grid{
            grid-template-columns:repeat(2, minmax(0,1fr));
        }
    }

    .usr-card{
        border-radius:22px;
        border:none;
        background:#ffffff;
        box-shadow:0 16px 40px rgba(148,163,184,.35);
        padding:22px 22px 20px;
        position:relative;
        overflow:hidden;
    }
    .usr-card::after{
        content:'';
        position:absolute;
        inset:auto -40px -40px auto;
        width:120px;
        height:120px;
        border-radius:999px;
        background:radial-gradient(circle at 30% 30%,rgba(129,140,248,.5),rgba(59,130,246,0));
        opacity:.6;
        pointer-events:none;
    }

    .usr-card h4{
        font-weight:700;
        margin-bottom:6px;
        color:#0f172a;
    }
    .usr-muted{
        color:#6b7280;
        font-size:.9rem;
    }

    .usr-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px;
        padding:4px 10px;
        font-size:.8rem;
        font-weight:600;
        background:#eef2ff;
        color:#4f46e5;
        margin-bottom:10px;
    }

    .usr-btn{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:9px 16px;
        border-radius:999px;
        text-decoration:none;
        font-weight:600;
        font-size:.9rem;
        border:1px solid transparent;
        transition:transform .12s ease, box-shadow .12s ease, filter .12s ease;
    }
    .usr-btn-primary{
        background:linear-gradient(120deg,#6366f1,#8b5cf6);
        color:#f9fafb;
        box-shadow:0 14px 32px rgba(79,70,229,.5);
    }
    .usr-btn-outline{
        background:#f9fafb;
        color:#1f2937;
        border-color:#e5e7eb;
        box-shadow:0 10px 24px rgba(148,163,184,.35);
    }
    .usr-btn:hover{
        transform:translateY(-1px);
        filter:brightness(1.03);
        box-shadow:0 18px 40px rgba(15,23,42,.18);
    }

    @media (max-width: 992px){
        .usr-bg{
            border-radius:0;
            padding:16px;
        }
    }
</style>

<div class="usr-bg">
    <div class="container-xxl" style="max-width:980px">
        {{-- Encabezado --}}
        <div class="mb-4">
            <h2 class="usr-title mb-1">
                <i class="bi bi-person-gear me-2"></i>
                Gestión de usuarios
            </h2>
            <p class="usr-subtitle mb-0">
                Elige la acción que deseas realizar dentro del módulo de usuarios.
            </p>
        </div>

        {{-- Tarjetas --}}
        <div class="usr-grid">
            {{-- Añadir usuario --}}
            <div class="usr-card">
                <div class="usr-pill">
                    <i class="bi bi-person-plus"></i>
                    Nuevo usuario
                </div>
                <h4>Registrar un usuario</h4>
                <p class="usr-muted mb-3">
                    Crea una nueva cuenta asignando nombre, correo y contraseña.
                    Ideal para docentes, auxiliares o nuevos administradores.
                </p>
                <a href="{{ route('register') }}" class="usr-btn usr-btn-primary">
                    <span>Ir a registro</span>
                    <i class="bi bi-arrow-right-short fs-5"></i>
                </a>
            </div>

            {{-- Eliminar usuario --}}
            <div class="usr-card">
                <div class="usr-pill" style="background:#fee2e2;color:#b91c1c;">
                    <i class="bi bi-trash3"></i>
                    Administración
                </div>
                <h4>Eliminar usuarios</h4>
                <p class="usr-muted mb-3">
                    Revisa la lista completa de cuentas y elimina aquellas que ya no se utilicen.
                    No podrás eliminarte a ti mismo ni al único administrador activo.
                </p>
                <a href="{{ route('usuarios.eliminar') }}" class="usr-btn usr-btn-outline">
                    <span>Ver lista de usuarios</span>
                    <i class="bi bi-arrow-right-short fs-5"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
