@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-3">Registro de Asistencias</h2>
    <button class="btn btn-primary mb-3"
        data-bs-toggle="modal"
        data-bs-target="#modalSeleccionarExportar"
        style="border-radius:10px;">
    📤 Exportar asistencia
</button>


    <div class="alert alert-info">
        <strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        <strong class="ms-3">🎓 Grado:</strong> {{ $grados->firstWhere('id', $grado_id)->nombre ?? '—' }}
        <strong class="ms-3">🏫 Sección:</strong> {{ $secciones->firstWhere('id', $seccion_id)->nombre ?? '—' }}
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('asistencias.store') }}">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha }}">

        <table class="table table-bordered text-center align-middle shadow-sm">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Alumno</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumnos as $alumno)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $alumno->nombre_apellido }}</td>
                        <td>{{ $alumno->grado->nombre ?? '—' }}</td>
                        <td>{{ $alumno->seccion->nombre ?? '—' }}</td>
                        <td>
                            @php $estado = $asistencias[$alumno->id]->estado ?? ''; @endphp
                            <select name="estado[{{ $alumno->id }}]" class="form-select">
                                <option value="">— Seleccionar —</option>
                                <option value="ASISTIÓ" {{ $estado === 'ASISTIÓ' ? 'selected' : '' }}>✅ ASISTIÓ</option>
                                <option value="FALTÓ" {{ $estado === 'FALTÓ' ? 'selected' : '' }}>❌ FALTÓ</option>
                                <option value="TARDE" {{ $estado === 'TARDE' ? 'selected' : '' }}>🕒 TARDE</option>
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">No hay alumnos registrados para este grado y sección.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-success">💾 Guardar Asistencias</button>
            <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">⬅️ Volver</a>
        </div>
    </form>
</div>
@endsection
