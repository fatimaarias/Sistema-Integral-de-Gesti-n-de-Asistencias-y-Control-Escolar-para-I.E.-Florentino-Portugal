<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de asistencias</title>
  <style>
    *{ font-family: DejaVu Sans, Arial, sans-serif; }
    h2{ text-align:center; margin:0 0 12px; }
    table{ width:100%; border-collapse:collapse; }
    th,td{ border:1px solid #444; padding:6px; font-size:12px; text-align:center; }
    th{ background:#eef3ff; }
    .meta{ margin-bottom:10px; font-size:12px; }
  </style>
</head>
<body>
  <h2>📋 Reporte de Asistencias</h2>

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
      @forelse($asistencias as $a)
        <tr>
          <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</td>
          <td>{{ $a->alumno->nombre_apellido }}</td>
          <td>{{ $a->alumno->grado->nombre ?? '—' }}</td>
          <td>{{ $a->alumno->seccion->nombre ?? '—' }}</td>
          <td>{{ $a->estado }}</td>
        </tr>
      @empty
        <tr><td colspan="5">Sin registros.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
