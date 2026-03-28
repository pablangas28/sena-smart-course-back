<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\FormularioInscripcionController;
use App\Http\Controllers\RegistroEstudianteController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\ReporteController;

// -------------------------------------------------------
// RUTAS PÚBLICAS (sin autenticación)
// -------------------------------------------------------

// Test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API SENA SmartCourse funcionando',
        'version' => '1.0.0'
    ]);
});

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Formulario de inscripción por token (estudiante accede desde link único)
Route::get('/inscripcion/{token}', [FormularioInscripcionController::class, 'showByToken']);
Route::post('/inscripcion/{token}', [RegistroEstudianteController::class, 'registrarPorToken']);

// -------------------------------------------------------
// RUTAS PROTEGIDAS (requieren token Sanctum)
// -------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);

    // -------------------------------------------------------
    // SOLO COORDINADOR
    // -------------------------------------------------------
    Route::middleware('role:coordinador')->group(function () {

        // Usuarios (crear instructores y aliados)
        Route::get('/usuarios', [UserController::class, 'index']);
        Route::post('/usuarios', [UserController::class, 'store']);
        Route::get('/usuarios/{user}', [UserController::class, 'show']);
        Route::patch('/usuarios/{user}/toggle-activo', [UserController::class, 'toggleActivo']);

        // Regionales
        Route::apiResource('/regionales', RegionalController::class);

        // Reportes
        Route::get('/reportes/cursos', [ReporteController::class, 'resumenCursos']);
        Route::get('/reportes/cursos/{curso}/pdf', [ReporteController::class, 'cursoPdf']);
        Route::get('/reportes/cursos/{curso}/asistencia-pdf', [ReporteController::class, 'asistenciaPdf']);
        Route::get('/reportes/cursos/{curso}/calificaciones-pdf', [ReporteController::class, 'calificacionesPdf']);
    });

    // -------------------------------------------------------
    // INSTRUCTOR Y ALIADO
    // -------------------------------------------------------
    Route::middleware('role:instructor,aliado')->group(function () {

        // Actualizar su propio perfil
        Route::patch('/usuarios/{user}', [UserController::class, 'update']);

        // Cursos
        Route::apiResource('/cursos', CursoController::class);

        // Clases de un curso
        Route::get('/cursos/{curso}/clases', [ClaseController::class, 'index']);
        Route::post('/cursos/{curso}/clases', [ClaseController::class, 'store']);
        Route::get('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'show']);
        Route::patch('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'update']);
        Route::delete('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'destroy']);

        // Formularios de inscripción
        Route::get('/cursos/{curso}/formularios', [FormularioInscripcionController::class, 'index']);
        Route::post('/cursos/{curso}/formularios', [FormularioInscripcionController::class, 'store']);
        Route::patch('/formularios/{formulario}/toggle-activo', [FormularioInscripcionController::class, 'toggleActivo']);

        // Estudiantes de un curso
        Route::get('/cursos/{curso_id}/estudiantes', [RegistroEstudianteController::class, 'index']);
        Route::get('/estudiantes/{registroEstudiante}', [RegistroEstudianteController::class, 'show']);
        Route::patch('/estudiantes/{registroEstudiante}/estado', [RegistroEstudianteController::class, 'cambiarEstado']);

        // Asistencia
        Route::get('/clases/{clase}/asistencia', [AsistenciaController::class, 'index']);
        Route::post('/clases/{clase}/asistencia', [AsistenciaController::class, 'store']);
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/asistencia', [AsistenciaController::class, 'porEstudiante']);

        // Calificaciones
        Route::get('/clases/{clase}/calificaciones', [CalificacionController::class, 'index']);
        Route::post('/clases/{clase}/calificaciones', [CalificacionController::class, 'store']);
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/calificaciones', [CalificacionController::class, 'promedioPorEstudiante']);
    });

    // -------------------------------------------------------
    // ESTUDIANTE
    // -------------------------------------------------------
    Route::middleware('role:estudiante')->group(function () {

        // Ver su propio progreso
        Route::get('/mi-progreso', function (Illuminate\Http\Request $request) {
            $user = $request->user();
            $registros = \App\Models\RegistroEstudiante::with('curso')
                ->where('user_id', $user->id)
                ->get();

            return response()->json($registros);
        });
    });
});