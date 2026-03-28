<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\RegistroEstudiante;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // Reporte general de todos los cursos (coordinador)
    public function resumenCursos()
    {
        $cursos = Curso::with(['regional', 'creadoPor', 'estudiantes'])
            ->withCount([
                'estudiantes',
                'estudiantes as activos'    => fn($q) => $q->where('estado', 'activo'),
                'estudiantes as desertados' => fn($q) => $q->where('estado', 'desertado'),
                'estudiantes as graduados'  => fn($q) => $q->where('estado', 'graduado'),
            ])
            ->get();

        return response()->json($cursos);
    }

    // Reporte de un curso específico en PDF
    public function cursoPdf(Curso $curso)
    {
        $curso->load([
            'regional',
            'creadoPor',
            'clases',
            'estudiantes.user',
        ]);

        $pdf = Pdf::loadView('reportes.curso', compact('curso'));

        return $pdf->download("reporte-curso-{$curso->id}.pdf");
    }

    // Reporte de asistencia de un curso en PDF
    public function asistenciaPdf(Curso $curso)
    {
        $curso->load(['clases.asistencias.estudiante', 'estudiantes']);

        $pdf = Pdf::loadView('reportes.asistencia', compact('curso'));

        return $pdf->download("asistencia-curso-{$curso->id}.pdf");
    }

    // Reporte de calificaciones de un curso en PDF
    public function calificacionesPdf(Curso $curso)
    {
        $curso->load(['clases.calificaciones.estudiante', 'estudiantes']);

        $pdf = Pdf::loadView('reportes.calificaciones', compact('curso'));

        return $pdf->download("calificaciones-curso-{$curso->id}.pdf");
    }
}