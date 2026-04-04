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

Route::get('/test', function () {
    return response()->json([
        'message' => 'API SENA SmartCourse funcionando',
        'version' => '1.0.0'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/inscripcion/{token}', [FormularioInscripcionController::class, 'showByToken']);
Route::post('/inscripcion/{token}', [RegistroEstudianteController::class, 'registrarPorToken']);

// -------------------------------------------------------
// RUTAS PROTEGIDAS (requieren token Sanctum)
// -------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth — todos los roles
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);

    // Actualizar perfil propio — todos los roles autenticados
    Route::patch('/usuarios/{user}', [UserController::class, 'update']);

    // -------------------------------------------------------
    // COORDINADOR, INSTRUCTOR Y ALIADO — lectura de cursos
    // El CursoController filtra internamente:
    //   - coordinador  → ve todos los cursos
    //   - instructor/aliado → solo los suyos (creado_por = user->id)
    // -------------------------------------------------------
    Route::middleware('role:coordinador,instructor,aliado')->group(function () {

        // Cursos
        Route::get('/cursos',        [CursoController::class, 'index']);
        Route::get('/cursos/{curso}', [CursoController::class, 'show']);

        // Clases — lectura
        Route::get('/cursos/{curso}/clases',             [ClaseController::class, 'index']);
        Route::get('/cursos/{curso}/clases/{clase}',     [ClaseController::class, 'show']);

        // Estudiantes — lectura
        Route::get('/cursos/{curso_id}/estudiantes',               [RegistroEstudianteController::class, 'index']);
        Route::get('/estudiantes/{registroEstudiante}',            [RegistroEstudianteController::class, 'show']);
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/asistencia',     [AsistenciaController::class, 'porEstudiante']);
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/calificaciones', [CalificacionController::class, 'promedioPorEstudiante']);

        // Asistencia y calificaciones — lectura
        Route::get('/clases/{clase}/asistencia',      [AsistenciaController::class, 'index']);
        Route::get('/clases/{clase}/calificaciones',  [CalificacionController::class, 'index']);
    });

    // -------------------------------------------------------
    // SOLO INSTRUCTOR Y ALIADO — escritura sobre cursos
    // -------------------------------------------------------
    Route::middleware('role:instructor,aliado')->group(function () {

        // Cursos — escritura
        Route::post('/cursos',           [CursoController::class, 'store']);
        Route::patch('/cursos/{curso}',  [CursoController::class, 'update']);
        Route::delete('/cursos/{curso}', [CursoController::class, 'destroy']);

        // Clases — escritura
        Route::post('/cursos/{curso}/clases',               [ClaseController::class, 'store']);
        Route::patch('/cursos/{curso}/clases/{clase}',      [ClaseController::class, 'update']);
        Route::delete('/cursos/{curso}/clases/{clase}',     [ClaseController::class, 'destroy']);

        // Formularios de inscripción
        Route::get('/cursos/{curso}/formularios',                    [FormularioInscripcionController::class, 'index']);
        Route::post('/cursos/{curso}/formularios',                   [FormularioInscripcionController::class, 'store']);
        Route::patch('/formularios/{formulario}/toggle-activo',      [FormularioInscripcionController::class, 'toggleActivo']);

        // Estudiantes — escritura
        Route::patch('/estudiantes/{registroEstudiante}/estado', [RegistroEstudianteController::class, 'cambiarEstado']);

        // Asistencia — escritura
        Route::post('/clases/{clase}/asistencia',     [AsistenciaController::class, 'store']);

        // Calificaciones — escritura
        Route::post('/clases/{clase}/calificaciones', [CalificacionController::class, 'store']);
    });

    // -------------------------------------------------------
    // SOLO COORDINADOR
    // -------------------------------------------------------
    Route::middleware('role:coordinador')->group(function () {

        // Usuarios
        Route::get('/usuarios',                          [UserController::class, 'index']);
        Route::post('/usuarios',                         [UserController::class, 'store']);
        Route::get('/usuarios/{user}',                   [UserController::class, 'show']);
        Route::patch('/usuarios/{user}/toggle-activo',   [UserController::class, 'toggleActivo']);

        // Regionales
        Route::apiResource('/regionales', RegionalController::class);

        // Reportes
        Route::get('/reportes/cursos',                              [ReporteController::class, 'resumenCursos']);
        Route::get('/reportes/cursos/{curso}/pdf',                  [ReporteController::class, 'cursoPdf']);
        Route::get('/reportes/cursos/{curso}/asistencia-pdf',       [ReporteController::class, 'asistenciaPdf']);
        Route::get('/reportes/cursos/{curso}/calificaciones-pdf',   [ReporteController::class, 'calificacionesPdf']);
    });

    // -------------------------------------------------------
    // SOLO ESTUDIANTE
    // -------------------------------------------------------
    Route::middleware('role:estudiante')->group(function () {

        Route::get('/mi-progreso', function (Illuminate\Http\Request $request) {
            $user = $request->user();
            $registros = \App\Models\RegistroEstudiante::with('curso.regional')
                ->where('user_id', $user->id)
                ->get();

            return response()->json($registros);
        });
    });
});