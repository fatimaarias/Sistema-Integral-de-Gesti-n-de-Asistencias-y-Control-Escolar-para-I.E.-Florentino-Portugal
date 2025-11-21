<!doctype html>
<html lang="es"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Asistencia registrada</title>
</head><body style="font-family:system-ui;margin:40px">
  <h2>✔ Asistencia registrada</h2>
  <p><strong>Alumno:</strong> {{ $alumno->nombre_apellido ?? $alumno->nombres.' '.$alumno->apellidos }}</p>
  <p><strong>Estado:</strong> {{ $estado }}</p>
  <p><strong>Hora:</strong> {{ $hora }}</p>
  <p>Puedes cerrar esta ventana.</p>
</body></html>
