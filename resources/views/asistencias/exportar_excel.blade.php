<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Alumno</th>
            <th>Grado</th>
            <th>Sección</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($asistencias as $asistencia)
            <tr>
                <td>{{ $asistencia->fecha }}</td>
                <td>{{ $asistencia->alumno->nombre_apellido }}</td>
                <td>{{ $asistencia->alumno->grado->nombre ?? '—' }}</td>
                <td>{{ $asistencia->alumno->seccion->nombre ?? '—' }}</td>
                <td>{{ $asistencia->estado }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
