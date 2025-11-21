@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <h2 class="fw-bold mb-4">Panel Principal</h2>

    <div class="card shadow p-4 mb-4">
        <h4 class="fw-bold">Reportes de Asistencias</h4>
        <p class="text-muted">Genera reportes en distintos formatos.</p>

        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalFormatos">
            📄 Exportar / Imprimir Asistencias
        </button>
    </div>

</div>

@include('asistencias.modales')
@endsection
