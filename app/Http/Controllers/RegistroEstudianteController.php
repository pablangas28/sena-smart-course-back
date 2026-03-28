<?php

namespace App\Http\Controllers;

use App\Models\FormularioInscripcion;
use App\Models\RegistroEstudiante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistroEstudianteController extends Controller
{
    // Estudiante se registra via token del formulario (público)
    public function registrarPorToken(Request $request, string $token)
    {
        $formulario = FormularioInscripcion::where('token', $token)->firstOrFail();

        if (!$formulario->estaVigente()) {
            return response()->json(['message' => 'Este formulario no está disponible.'], 403);
        }

        $request->validate([
            'nombre'                  => 'required|string|max:100',
            'apellidos'               => 'required|string|max:100',
            'email'                   => 'required|email|unique:users',
            'password'                => 'required|min:8',
            'fecha_nacimiento'        => 'required|date',
            'genero'                  => 'required|in:masculino,femenino,otro',
            'celular'                 => 'required|string|max:20',
            'telefono'                => 'nullable|string|max:20',
            'cel_contacto_emergencia' => 'required|string|max:20',
            'tel_contacto_emergencia' => 'nullable|string|max:20',
            'pantallazo_sofia'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Crear cuenta del estudiante
        $user = User::create([
            'nombre'    => $request->nombre,
            'apellidos' => $request->apellidos,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'rol'       => 'estudiante',
        ]);

        // Guardar pantallazo sofia si fue enviado
        $rutaSofia = null;
        if ($request->hasFile('pantallazo_sofia')) {
            $rutaSofia = $request->file('pantallazo_sofia')
                ->store('sofia', 'public');
        }

        // Crear registro en el curso
        $registro = RegistroEstudiante::create([
            'user_id'                 => $user->id,
            'curso_id'                => $formulario->curso_id,
            'nombre'                  => $request->nombre,
            'apellidos'               => $request->apellidos,
            'fecha_nacimiento'        => $request->fecha_nacimiento,
            'genero'                  => $request->genero,
            'celular'                 => $request->celular,
            'telefono'                => $request->telefono,
            'cel_contacto_emergencia' => $request->cel_contacto_emergencia,
            'tel_contacto_emergencia' => $request->tel_contacto_emergencia,
            'pantallazo_sofia'        => $rutaSofia,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'  => 'Registro exitoso.',
            'token'    => $token,
            'user'     => $user,
            'registro' => $registro,
        ], 201);
    }

    // Listar estudiantes de un curso
    public function index(Request $request, $curso_id)
    {
        $estudiantes = RegistroEstudiante::with('user')
            ->where('curso_id', $curso_id)
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->get();

        return response()->json($estudiantes);
    }

    // Ver detalle de un registro
    public function show(RegistroEstudiante $registroEstudiante)
    {
        return response()->json($registroEstudiante->load('user', 'curso'));
    }

    // Cambiar estado del estudiante (activo, desertado, graduado)
    public function cambiarEstado(Request $request, RegistroEstudiante $registroEstudiante)
    {
        $request->validate([
            'estado' => 'required|in:activo,desertado,graduado',
        ]);

        $registroEstudiante->update(['estado' => $request->estado]);

        return response()->json(['message' => 'Estado actualizado correctamente.']);
    }
}