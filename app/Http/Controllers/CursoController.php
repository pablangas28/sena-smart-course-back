<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $cursos = Curso::with(['regional', 'creadoPor'])
            ->when(!$user->esCoordinador(), function ($q) use ($user) {
                if ($user->esEstudiante()) {
                    $q->whereHas('estudiantes', function ($q2) use ($user) {
                        $q2->where('user_id', $user->id);
                    });
                } else {
                    $q->where('creado_por', $user->id);
                }
            })
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->get();

        return response()->json($cursos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:150',
            'descripcion'      => 'nullable|string',
            'regional_id'      => 'required|exists:regionales,id',
            'horas_requeridas' => 'sometimes|integer|min:1',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'nullable|date|after:fecha_inicio',
        ]);

        $curso = Curso::create([
            'nombre'           => $request->nombre,
            'descripcion'      => $request->descripcion,
            'creado_por'       => $request->user()->id,
            'regional_id'      => $request->regional_id,
            'horas_requeridas' => $request->horas_requeridas ?? 40,
            'fecha_inicio'     => $request->fecha_inicio,
            'fecha_fin'        => $request->fecha_fin,
        ]);

        return response()->json($curso, 201);
    }

    public function show(Request $request, Curso $curso)
    {
        $user = $request->user();

        if (!$user->puedeVerCurso($curso)) {
            return response()->json(['message' => 'No tienes permiso para ver este curso.'], 403);
        }

        $relations = ['regional', 'creadoPor', 'clases'];
        if (!$user->esEstudiante()) {
            $relations[] = 'estudiantes';
        }

        return response()->json($curso->load($relations));
    }

    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'nombre'      => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'estado'      => 'sometimes|in:activo,finalizado,cancelado',
            'fecha_fin'   => 'nullable|date',
        ]);

        $curso->update($request->only([
            'nombre', 'descripcion', 'estado', 'fecha_fin'
        ]));

        return response()->json($curso);
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();

        return response()->json(['message' => 'Curso eliminado correctamente.']);
    }
}