@extends('layouts.app')

@section('content')
<style>
    /* 💠 Fondo general mejorado */
    .alumnos-bg{
        background: #f1f5f9;
        border-radius:32px;
        padding:24px;
        margin-top:20px;
        box-shadow:0 18px 40px rgba(15,23,42,0.12);
    }

    /* 🧭 Cabecera */
    .alumnos-title{
        font-weight:800;
        font-size:1.55rem;
        color:#0f172a;
    }

    .badge-curso{
        background:#e0e7ff;
        color:#3730a3;
        border-radius:999px;
        padding:4px 12px;
        font-size:.8rem;
        font-weight:600;
    }

    .search-box{
        position:relative;
    }

    .search-box input{
        padding-left:32px;
        border-radius:999px;
        border:none;
        box-shadow:0 4px 10px rgba(148,163,184,.35);
        background:white;
        font-weight:500;
    }

    .search-box i{
        position:absolute;
        left:10px;
        top:50%;
        transform:translateY(-50%);
        color:#64748b;
    }

    /* Tabla */
    table.table-ranking{
        width:100%;
        border-collapse:separate;
        border-spacing:0 10px;
    }

    .table-ranking thead th{
        color:#475569;
        font-size:.82rem;
        font-weight:700;
        text-transform:uppercase;
        padding:4px 14px;
    }

    .table-ranking tbody tr{
        background:#ffffff;
        box-shadow:0 12px 24px rgba(71,85,105,0.15);
        border-radius:16px;
        overflow:hidden;
    }

    .table-ranking td{
        border:none;
        padding:12px 18px;
        color:#1e293b;
        font-weight:500;
        font-size:.95rem;
    }

    /* Ranking */
    .rank-pill{
        width:30px;
        height:30px;
        border-radius:50%;
        font-weight:800;
        font-size:.82rem;
        display:flex;
        align-items:center;
        justify-content:center;
        background:#fff7ed;
        color:#ea580c;
        border:1px solid #fb923c;
    }

    /* Avatar */
    .avatar-inicial{
        width:34px;
        height:34px;
        border-radius:999px;
        display:flex;
        justify-content:center;
        align-items:center;
        color:white;
        font-weight:700;
        background:linear-gradient(135deg,#6366f1,#8b5cf6);
    }

    /* Nombre alumno */
    .name-main{
        font-size:1rem;
        font-weight:700;
        color:#0f172a;
    }

    .name-sub{
        color:#475569;
        font-size:.80rem;
        font-weight:500;
    }

    /* Género */
    .tag-genero{
        padding:4px 12px;
        border-radius:999px;
        font-size:.75rem;
        font-weight:700;
        color:white !important;
        background:#0ea5e9;
    }

    .tag-genero.femenino{
        background:#ec4899 !important;
    }

    /* Botón editar */
    .btn-edit{
        border-radius:999px;
        font-weight:600;
        padding:6px 16px;
    }
</style>

<div class="alumnos-bg">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
        <div>
            <h2 class="alumnos-title">👥 Lista de alumnos — {{ $grado->nombre }} {{ $seccion->nombre }}</h2>
            <span class="badge-curso">{{ $alumnos->count() }} alumno(s) registrados</span>
        </div>

        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchAlumno" class="form-control form-control-sm" placeholder="Buscar por nombre...">
        </div>
    </div>

    <form id="multiDeleteForm" action="{{ route('alumnos.destroyMany') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="table-wrapper">
            <table class="table-ranking">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Ranking</th>
                        <th>Alumno</th>
                        <th>Género</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($alumnos as $i => $fila)
                <tr data-nombre="{{ Str::lower($fila->nombre_apellido) }}">

                    <td><input type="checkbox" class="chk" name="ids[]" value="{{ $fila->id }}"></td>

                    <td><div class="rank-pill">{{ $i + 1 }}</div></td>

                    <td>
                        <div class="name-block d-flex align-items-center gap-2">
                            <div class="avatar-inicial">{{ strtoupper(substr($fila->nombre_apellido,0,1)) }}</div>

                            <div>
                                <div class="name-main">{{ $fila->nombre_apellido }}</div>
                                <div class="name-sub">{{ $grado->nombre }} {{ $seccion->nombre }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @php 
                            $fem = in_array($fila->genero, ['F','FEMENINO']); 
                        @endphp

                        <span class="tag-genero {{ $fem ? 'femenino' : '' }}">
                            {{ $fem ? 'Femenino' : 'Masculino' }}
                        </span>
                    </td>

                    <td>{{ $fila->direccion ?? 'Sin dirección registrada' }}</td>

                    <td>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary btn-edit"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                                onclick="cargarAlumno({{ $fila->id }}, '{{ $fila->nombre_apellido }}', '{{ $fila->genero }}', `{{ $fila->direccion }}`)">
                            <i class="bi bi-pencil-square"></i> Editar
                        </button>
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No hay alumnos registrados en esta sección.</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('alumnos.seleccionar') }}" class="btn btn-outline-secondary">← Volver</a>

            <button type="submit" class="btn btn-danger" id="btnDelete" disabled>
                Eliminar seleccionados (<span id="selCount">0</span>)
            </button>
        </div>

    </form>
</div>

<!-- 🟣 MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px;">

            <form method="POST" id="formEditar">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre y apellidos</label>
                        <input type="text" class="form-control" name="nombre_apellido" id="edit_nombre" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Género</label>
                        <select class="form-select" name="genero" id="edit_genero">
                            <option value="">Sin especificar</option>
                            <option value="MASCULINO">Masculino</option>
                            <option value="FEMENINO">Femenino</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" id="edit_direccion">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function cargarAlumno(id, nombre, genero, direccion){
    document.getElementById('formEditar').action = `/alumnos/${id}`;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_genero').value = genero ?? '';
    document.getElementById('edit_direccion').value = direccion ?? '';
}

document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('checkAll');
    const checks = [...document.querySelectorAll('.chk')];
    const counter = document.getElementById('selCount');
    const deleteBtn = document.getElementById('btnDelete');
    const searchInput = document.getElementById('searchAlumno');
    const rows = [...document.querySelectorAll('tbody tr')];

    selectAll.addEventListener('change', e => {
        checks.forEach(c => c.checked = e.target.checked);
        updateCount();
    });

    checks.forEach(c => c.addEventListener('change', updateCount));

    function updateCount(){
        const n = checks.filter(c => c.checked).length;
        counter.textContent = n;
        deleteBtn.disabled = n === 0;
    }

    searchInput.addEventListener('keyup', () => {
        const term = searchInput.value.toLowerCase().trim();
        rows.forEach(row => {
            row.style.display = row.dataset.nombre.includes(term) ? '' : 'none';
        });
    });
});
</script>

@endsection
