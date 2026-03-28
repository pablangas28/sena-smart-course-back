<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\Curso;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    public function index(Curso $curso)
    {
        return response()->json($curso->clases()->orderBy('fecha_hora')->get());
    }

    public function store(Request $request, Curso $curso)
    {
        $request->validate([
            'tema'           => 'required|string|max:200',
            'fecha_hora'     => 'required|date',
            'tipo'           => 'required|in:presencial,virtual',
            'duracion_horas' => 'sometimes|integer|min:1',
            // TODO: agregar validaciones aquí si en el futuro
            // se agregan campos como link_reunion, sala, materiales, etc.
        ]);

        $clase = $curso->clases()->create([
            'tema'           => $request->tema,
            'fecha_hora'     => $request->fecha_hora,
            'tipo'           => $request->tipo,
            'duracion_horas' => $request->duracion_horas ?? 2,
        ]);

        // Actualizar horas cumplidas del curso automáticamente
        $curso->actualizarHorasCumplidas();

        return response()->json($clase, 201);
    }

    public function show(Curso $curso, Clase $clase)
    {
        return response()->json($clase->load(['asistencias', 'calificaciones']));
    }

    public function update(Request $request, Curso $curso, Clase $clase)
    {
        $request->validate([
            'tema'           => 'sometimes|string|max:200',
            'fecha_hora'     => 'sometimes|date',
            'tipo'           => 'sometimes|in:presencial,virtual',
            'duracion_horas' => 'sometimes|integer|min:1',
        ]);

        $clase->update($request->only([
            'tema', 'fecha_hora', 'tipo', 'duracion_horas'
        ]));

        // Recalcular horas cumplidas
        $curso->actualizarHorasCumplidas();

        return response()->json($clase);
    }

    public function destroy(Curso $curso, Clase $clase)
    {
        $clase->delete();

        // Recalcular horas cumplidas
        $curso->actualizarHorasCumplidas();

        return response()->json(['message' => 'Clase eliminada correctamente.']);
    }
}