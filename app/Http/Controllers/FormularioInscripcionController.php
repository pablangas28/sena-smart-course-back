<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\FormularioInscripcion;
use Illuminate\Http\Request;

class FormularioInscripcionController extends Controller
{
    // Crear formulario con link único para un curso
    public function store(Request $request, Curso $curso)
    {
        $request->validate([
            'expira_en' => 'nullable|date|after:now',
        ]);

        $formulario = FormularioInscripcion::create([
            'curso_id'   => $curso->id,
            'creado_por' => $request->user()->id,
            'expira_en'  => $request->expira_en,
        ]);

        return response()->json([
            'formulario' => $formulario,
            'link' => config('app.url') . "/api/inscripcion/{$formulario->token}",
        ], 201);
    }

    // Ver formulario por token (público, sin autenticación)
    public function showByToken(string $token)
    {
        $formulario = FormularioInscripcion::with('curso.regional')
            ->where('token', $token)
            ->firstOrFail();

        if (!$formulario->estaVigente()) {
            return response()->json(['message' => 'Este formulario no está disponible.'], 403);
        }

        return response()->json($formulario);
    }

    // Listar formularios de un curso
    public function index(Curso $curso)
    {
        return response()->json($curso->formularios()->with('creadoPor')->get());
    }

    // Activar o desactivar formulario
    public function toggleActivo(FormularioInscripcion $formulario)
    {
        $formulario->update(['activo' => !$formulario->activo]);

        $estado = $formulario->activo ? 'activado' : 'desactivado';

        return response()->json(['message' => "Formulario {$estado} correctamente."]);
    }
}