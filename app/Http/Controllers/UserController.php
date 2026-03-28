<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Solo coordinador puede listar usuarios
    public function index(Request $request)
    {
        $usuarios = User::with('regional')
            ->when($request->rol, fn($q) => $q->where('rol', $request->rol))
            ->when($request->regional_id, fn($q) => $q->where('regional_id', $request->regional_id))
            ->get();

        return response()->json($usuarios);
    }

    // Coordinador crea instructores y aliados
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellidos'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users',
            'telefono'    => 'nullable|string|max:20',
            'ocupacion'   => 'nullable|string|max:100',
            'rol'         => 'required|in:instructor,aliado',
            'regional_id' => 'nullable|exists:regionales,id',
            'password'    => 'required|min:8',
        ]);

        $usuario = User::create([
            'nombre'      => $request->nombre,
            'apellidos'   => $request->apellidos,
            'email'       => $request->email,
            'telefono'    => $request->telefono,
            'ocupacion'   => $request->ocupacion,
            'rol'         => $request->rol,
            'regional_id' => $request->regional_id,
            'password'    => Hash::make($request->password),
        ]);

        return response()->json($usuario, 201);
    }

    // Ver un usuario específico
    public function show(User $user)
    {
        return response()->json($user->load('regional'));
    }

    // Instructor/aliado actualiza su propio perfil
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'apellidos'   => 'sometimes|string|max:100',
            'telefono'    => 'nullable|string|max:20',
            'ocupacion'   => 'nullable|string|max:100',
            'regional_id' => 'nullable|exists:regionales,id',
        ]);

        $user->update($request->only([
            'nombre', 'apellidos', 'telefono', 'ocupacion', 'regional_id'
        ]));

        return response()->json($user);
    }

    // Coordinador activa o desactiva una cuenta
    public function toggleActivo(User $user)
    {
        $user->update(['activo' => !$user->activo]);

        $estado = $user->activo ? 'activado' : 'desactivado';

        return response()->json(['message' => "Usuario {$estado} correctamente."]);
    }
}