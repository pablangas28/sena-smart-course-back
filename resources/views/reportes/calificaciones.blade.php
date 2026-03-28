<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { background-color: #39A900; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; font-size: 13px; }
        .section { margin: 20px 0; }
        .section h2 { color: #39A900; border-bottom: 2px solid #39A900; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #39A900; color: white; padding: 8px; text-align: left; }
        td { padding: 7px 8px; border-bottom: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .nota-alta { color: #155724; font-weight: bold; }
        .nota-media { color: #856404; font-weight: bold; }
        .nota-baja { color: #721c24; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1>SENA SmartCourse</h1>
    <p>Reporte de Calificaciones — {{ $curso->nombre }}</p>
</div>

@foreach($curso->clases as $clase)
<div class="section">
    <h2>{{ $clase->tema }} — {{ $clase->fecha_hora->format('d/m/Y H:i') }}</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Estudiante</th>
                <th>Nota</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clase->calificaciones as $index => $calificacion)
            @php
                $notaClass = $calificacion->nota >= 4 ? 'nota-alta' : ($calificacion->nota >= 3 ? 'nota-media' : 'nota-baja');
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $calificacion->estudiante->nombre }} {{ $calificacion->estudiante->apellidos }}</td>
                <td class="{{ $notaClass }}">{{ number_format($calificacion->nota, 1) }}</td>
                <td>{{ $calificacion->observacion ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;">Sin calificaciones registradas</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

<div class="footer">
    Generado el {{ now()->format('d/m/Y H:i') }} — SENA SmartCourse
</div>

</body>
</html>
