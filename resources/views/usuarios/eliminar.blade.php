@extends('layouts.app')

@section('content')
@php
    $totalUsers   = $users->count();
    $adminsTotal  = \App\Models\User::where('role', 'admin')->count();
@endphp

<style>
    .users-bg{
        background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 40%,#f9fafb 100%);
        min-height: calc(100vh - 80px);
        border-radius: 32px;
        padding: 24px;
    }

    .users-title{
        font-weight:700;
        font-size:1.6rem;
        color:#0f172a;
    }
    .users-subtitle{
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

    .users-card{
        border-radius:22px;
        border:none;
        background:#ffffff;
        box-shadow:0 18px 40px rgba(148,163,184,.35);
        overflow:hidden;
    }

    .users-toolbar{
        padding:14px 18px 8px;
        border-bottom:1px solid #e5e7eb;
        display:flex;
        flex-wrap:wrap;
        gap:.75rem;
        justify-content:space-between;
        align-items:center;
    }

    .search-wrap{
        position:relative;
        max-width:260px;
        width:100%;
    }
    .search-wrap i{
        position:absolute;
        left:11px;
        top:50%;
        transform:translateY(-50%);
        font-size:.9rem;
        color:#9ca3af;
    }
    .users-search{
        border-radius:999px;
        border:1px solid #d1d5db;
        padding:.45rem .8rem .45rem 30px;
        font-size:.9rem;
        width:100%;
    }

    thead.users-head{
        background:linear-gradient(90deg,#e0f2fe,#eef2ff);
    }
    thead.users-head th{
        border-bottom:none;
        font-size:.8rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#6b7280;
    }

    .users-row:hover{
        background:#f9fafb;
    }

    .role-pill{
        border-radius:999px;
        padding:.18rem .65rem;
        font-size:.75rem;
        font-weight:600;
        background:#e5e7eb;
        color:#374151;
    }
    .role-pill.admin{
        background:#f973161a;
        color:#ea580c;
    }

    .btn-back{
        border-radius:999px;
        padding-inline:18px;
        font-weight:500;
    }

    @media (max-width: 992px){
        .users-bg{
            border-radius:0;
            padding:16px;
        }
        .users-toolbar{
            align-items:flex-start;
        }
    }
</style>

<div class="users-bg">
    <div class="container-xxl">

        {{-- Encabezado --}}
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3">
            <div>
                <h2 class="users-title mb-1">
                    <i class="bi bi-people-fill me-2"></i> Gestión de usuarios
                </h2>
                <p class="users-subtitle mb-0">
                    Elimina cuentas que ya no se usan. No puedes eliminarte a ti mismo ni al único administrador.
                </p>
            </div>

            <div class="mt-3 mt-lg-0">
                <span class="chip-soft">
                    <i class="bi bi-person-badge"></i>
                    <span>{{ $totalUsers }} usuario(s) registrados</span>
                </span>
            </div>
        </div>

        {{-- Tarjeta principal --}}
        <div class="card users-card">
            {{-- Toolbar superior --}}
            <div class="users-toolbar">
                <span class="text-muted" style="font-size:.82rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Los administradores están protegidos si solo queda uno activo.
                </span>

                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           id="userSearch"
                           class="users-search"
                           placeholder="Buscar por nombre o correo...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="users-head">
                        <tr>
                            <th style="width:70px">ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th style="width:120px">Rol</th>
                            <th style="width:140px" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usersBody">
                        @forelse ($users as $u)
                            @php
                                $soyYo      = auth()->id() === $u->id;
                                $unicoAdmin = $u->role === 'admin' && $adminsTotal <= 1;
                            @endphp
                            <tr class="users-row"
                                data-search="{{ strtolower($u->name.' '.$u->email.' '.$u->role) }}">
                                <td>{{ $u->id }}</td>
                                <td class="fw-semibold">{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="role-pill {{ $u->role === 'admin' ? 'admin' : '' }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if(!$soyYo && !$unicoAdmin)
                                        <form action="{{ route('usuarios.destroy', $u) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar a {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-shield-lock me-1"></i>
                                            No permitido
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-back">
                <i class="bi bi-arrow-left-circle me-1"></i>
                Volver
            </a>
        </div>
    </div>
</div>

{{-- Buscador en vivo --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('userSearch');
    const rows  = Array.from(document.querySelectorAll('#usersBody tr.users-row'));

    if (!input) return;

    input.addEventListener('input', () => {
        const q = (input.value || '').toLowerCase().trim();
        if (!q) {
            rows.forEach(r => r.style.display = '');
            return;
        }
        rows.forEach(r => {
            const txt = r.getAttribute('data-search') || '';
            r.style.display = txt.includes(q) ? '' : 'none';
        });
    });
});
</script>
@endsection
