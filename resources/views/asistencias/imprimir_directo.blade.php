<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            margin-top:40px;
            text-align:left;
        }
        table {
            width:100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border:1px solid #444;
            padding:6px;
            font-size: 13px;
            text-align:center;
        }
        th {
            background:#eaeaea;
        }
        .pagina {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <h1 style="text-align:center; margin-bottom:30px;">
        📋 Reporte de Asistencias
    </h1>

    @php
        // AGRUPAMOS ASISTENCIAS POR GRADO → SECCIÓN
        $agrupado = $asistencias->groupBy([
            fn($item) => $item->alumno->grado->id,
            fn($item) => $item->alumno->seccion->id
        ]);
    @endphp

    @foreach($agrupado as $grado_id => $porGrado)
        @foreach($porGrado as $seccion_id => $items)

            @php
                $grado   = $items->first()->alumno->grado->nombre;
                $seccion = $items->first()->alumno->seccion->nombre;
            @endphp

            <div class="pagina">
                <h2>
                    Grado: {{ $grado }} — Sección: {{ $seccion }}
                </h2>

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
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->fecha }}</td>
                                <td>{{ $item->alumno->nombre_apellido }}</td>
                                <td>{{ $item->alumno->grado->nombre }}</td>
                                <td>{{ $item->alumno->seccion->nombre }}</td>
                                <td>{{ $item->estado }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @endforeach
    @endforeach
    
    <script>
        // 🔥 Abre el cuadro de impresión automáticamente
        window.onload = function() {
            setTimeout(() => window.print(), 1000);
        }
    </script>
</body>
</html>
