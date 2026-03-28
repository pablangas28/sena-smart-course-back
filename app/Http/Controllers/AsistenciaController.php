<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Clase;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    // Registrar asistencia de todos los estudiantes de una clase
    public function store(Request $request, Clase $clase)
    {
        $request->validate([
            'asistencias'                => 'required|array',
            'asistencias.*.estudiante_id'=> 'required|exists:users,id',
            'asistencias.*.asistio'      => 'required|boolean',
            'asistencias.*.observacion'  => 'nullable|string',
        ]);

        foreach ($request->asistencias as $item) {
            Asistencia::updateOrCreate(
                [
                    'clase_id'      => $clase->id,
                    'estudiante_id' => $item['estudiante_id'],
                ],
                [
                    'asistio'     => $item['asistio'],
                    'observacion' => $item['observacion'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Asistencia registrada correctamente.']);
    }

    // Ver asistencia de una clase
    public function index(Clase $clase)
    {
        $asistencias = $clase->asistencias()->with('estudiante')->get();

        return response()->json($asistencias);
    }

    // Ver asistencia de un estudiante en un curso completo
    public function porEstudiante(Request $request, $curso_id, $estudiante_id)
    {
        $asistencias = Asistencia::whereHas('clase', fn($q) => $q->where('curso_id', $curso_id))
            ->where('estudiante_id', $estudiante_id)
            ->with('clase')
            ->get();

        $total    = $asistencias->count();
        $asistio  = $asistencias->where('asistio', true)->count();
        $porcentaje = $total > 0 ? round(($asistio / $total) * 100, 1) : 0;

        return response()->json([
            'asistencias' => $asistencias,
            'resumen'     => [
                'total_clases'  => $total,
                'clases_asistidas' => $asistio,
                'porcentaje'    => $porcentaje,
            ],
        ]);
    }
}