
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        .header { background-color: #39A900; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0; font-size: 13px; }
        .section { margin: 20px 0; }
        .section h2 { color: #39A900; border-bottom: 2px solid #39A900; padding-bottom: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .info-item { margin-bottom: 8px; }
        .info-item span { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #39A900; color: white; padding: 8px; text-align: left; }
        td { padding: 7px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .badge-activo { background-color: #d4edda; color: #155724; }
        .badge-desertado { background-color: #f8d7da; color: #721c24; }
        .badge-graduado { background-color: #cce5ff; color: #004085; }
        .horas { text-align: center; margin: 20px 0; padding: 15px; background: #f0f9f0; border: 2px solid #39A900; border-radius: 8px; }
        .horas h3 { margin: 0; color: #39A900; font-size: 18px; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1>SENA SmartCourse</h1>
    <p>Reporte General del Curso</p>
</div>

<div class="section">
    <h2>Información del Curso</h2>
    <div class="info-item"><span>Nombre:</span> {{ $curso->nombre }}</div>
    <div class="info-item"><span>Descripción:</span> {{ $curso->descripcion ?? 'Sin descripción' }}</div>
    <div class="info-item"><span>Regional:</span> {{ $curso->regional->nombre ?? 'N/A' }}</div>
    <div class="info-item"><span>Instructor/Aliado:</span> {{ $curso->creadoPor->nombre }} {{ $curso->creadoPor->apellidos }}</div>
    <div class="info-item"><span>Fecha inicio:</span> {{ $curso->fecha_inicio->format('d/m/Y') }}</div>
    <div class="info-item"><span>Fecha fin:</span> {{ $curso->fecha_fin ? $curso->fecha_fin->format('d/m/Y') : 'En curso' }}</div>
    <div class="info-item"><span>Estado:</span> {{ ucfirst($curso->estado) }}</div>
</div>

<div class="horas">
    <h3>Horas cumplidas: {{ $curso->horas_cumplidas }} / {{ $curso->horas_requeridas }}</h3>
</div>

<div class="section">
    <h2>Estudiantes ({{ $curso->estudiantes->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Celular</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($curso->estudiantes as $index => $estudiante)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $estudiante->nombre }}</td>
                <td>{{ $estudiante->apellidos }}</td>
                <td>{{ $estudiante->celular }}</td>
                <td>
                    <span class="badge badge-{{ $estudiante->estado }}">
                        {{ ucfirst($estudiante->estado) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">No hay estudiantes registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    Generado el {{ now()->format('d/m/Y H:i') }} — SENA SmartCourse
</div>

</body>
</html>