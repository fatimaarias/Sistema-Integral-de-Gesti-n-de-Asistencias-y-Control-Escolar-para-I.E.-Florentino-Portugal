@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-3">🖨️ Reporte de Asistencia</h2>

    <p>
        <strong>Grado:</strong> {{ $grado }}° <br>
        <strong>Sección:</strong> {{ $seccion }}
    </p>

    <table class="table table-bordered shadow-sm mt-3">
        <thead class="table-secondary">
            <tr>
                <th>Fecha</th>
                <th>Alumno</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($asistencias as $asistencia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $asistencia->alumno->nombre_apellido }}</td>
                    <td>{{ $asistencia->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        No hay asistencias registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">⬅️ Volver</a>
</div>
@endsection
