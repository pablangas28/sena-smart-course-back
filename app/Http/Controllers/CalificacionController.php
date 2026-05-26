<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Clase;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    // Registrar calificaciones de una clase
    public function store(Request $request, Clase $clase)
    {
        $request->validate([
            'calificaciones'                => 'required|array',
            'calificaciones.*.estudiante_id'=> 'required|exists:users,id',
            'calificaciones.*.nota'         => 'required|numeric|min:0|max:5',
            'calificaciones.*.observacion'  => 'nullable|string',
            // TODO: si en el futuro se agregan múltiples actividades por clase,
            // agregar validación: 'calificaciones.*.actividad' => 'required|string'
        ]);

        foreach ($request->calificaciones as $item) {
            Calificacion::updateOrCreate(
                [
                    'clase_id'      => $clase->id,
                    'estudiante_id' => $item['estudiante_id'],
                ],
                [
                    'nota'        => $item['nota'],
                    'observacion' => $item['observacion'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Calificaciones registradas correctamente.']);
    }

    // Ver calificaciones de una clase
    public function index(Request $request, Clase $clase)
    {
        if (!$request->user()->puedeVerCurso($clase->curso)) {
            return response()->json(['message' => 'No tienes permiso para ver estas calificaciones.'], 403);
        }

        return response()->json($clase->calificaciones()->with('estudiante')->get());
    }

    // Promedio final de un estudiante en un curso
    public function promedioPorEstudiante(Request $request, $curso_id, $estudiante_id)
    {
        $curso = \App\Models\Curso::findOrFail($curso_id);
        if (!$request->user()->puedeVerCurso($curso)) {
            return response()->json(['message' => 'No tienes permiso para ver estas calificaciones.'], 403);
        }

        $calificaciones = Calificacion::whereHas('clase', fn($q) => $q->where('curso_id', $curso_id))
            ->where('estudiante_id', $estudiante_id)
            ->get();

        $promedio = $calificaciones->avg('nota');

        return response()->json([
            'calificaciones' => $calificaciones,
            'promedio_final' => round($promedio, 1),
        ]);
    }
}