<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;

class RegionalController extends Controller
{
    public function index()
    {
        return response()->json(Regional::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
        ]);

        $regional = Regional::create($request->only('nombre', 'departamento'));

        return response()->json($regional, 201);
    }

    public function show(Regional $regional)
    {
        return response()->json($regional->load('usuarios', 'cursos'));
    }

    public function update(Request $request, Regional $regional)
    {
        $request->validate([
            'nombre'       => 'sometimes|string|max:100',
            'departamento' => 'sometimes|string|max:100',
        ]);

        $regional->update($request->only('nombre', 'departamento'));

        return response()->json($regional);
    }

    public function destroy(Regional $regional)
    {
        $regional->delete();

        return response()->json(['message' => 'Regional eliminada correctamente.']);
    }
}