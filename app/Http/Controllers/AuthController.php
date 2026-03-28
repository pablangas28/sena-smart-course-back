<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'message' => 'Tu cuenta está inactiva.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'nombre'   => $user->nombre,
                'apellidos' => $user->apellidos,
                'email'    => $user->email,
                'rol'      => $user->rol,
                'regional' => $user->regional,
            ]
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    // Datos del usuario autenticado
    public function me(Request $request)
    {
        return response()->json($request->user()->load('regional'));
    }

    // Cambiar contraseña
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nuevo'  => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->password_actual, $request->user()->password)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta.'], 422);
        }

        $request->user()->update([
            'password' => Hash::make($request->password_nuevo),
        ]);

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}
