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

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'message' => 'API SENA SmartCourse funcionando',
        'version' => '1.0.0'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/inscripcion/{token}', [FormularioInscripcionController::class, 'showByToken']);
Route::post('/inscripcion/{token}', [RegistroEstudianteController::class, 'registrarPorToken']);


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);

    Route::patch('/usuarios/{user}', [UserController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | REGIONALES (LECTURA PARA VARIOS ROLES)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:coordinador,instructor,aliado')->group(function () {
        Route::get('/regionales', [RegionalController::class, 'index']);
        Route::get('/regionales/{regional}', [RegionalController::class, 'show']);
    });

    /*
    |--------------------------------------------------------------------------
    | CURSOS Y CLASES (LECTURA PARA TODOS, INCLUYENDO ESTUDIANTE)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:coordinador,instructor,aliado,estudiante')->group(function () {

        Route::get('/cursos', [CursoController::class, 'index']);
        Route::get('/cursos/{curso}', [CursoController::class, 'show']);

        // Clases
        Route::get('/cursos/{curso}/clases', [ClaseController::class, 'index']);
        Route::get('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'show']);

        // Asistencias y Calificaciones
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/asistencia', [AsistenciaController::class, 'porEstudiante']);
        Route::get('/clases/{clase}/asistencia', [AsistenciaController::class, 'index']);
        
        Route::get('/cursos/{curso_id}/estudiantes/{estudiante_id}/calificaciones', [CalificacionController::class, 'promedioPorEstudiante']);
        Route::get('/clases/{clase}/calificaciones', [CalificacionController::class, 'index']);
    });

    /*
    |--------------------------------------------------------------------------
    | ESTUDIANTES Y METRICAS (LECTURA - SIN ESTUDIANTE)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:coordinador,instructor,aliado')->group(function () {

        // Estudiantes
        Route::get('/cursos/{curso_id}/estudiantes', [RegistroEstudianteController::class, 'index']);
        Route::get('/estudiantes/{registroEstudiante}', [RegistroEstudianteController::class, 'show']);

    });

    /*
    |--------------------------------------------------------------------------
    | INSTRUCTOR Y ALIADO (ESCRITURA)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:instructor,aliado')->group(function () {

        // Cursos
        Route::post('/cursos', [CursoController::class, 'store']);
        Route::patch('/cursos/{curso}', [CursoController::class, 'update']);
        Route::delete('/cursos/{curso}', [CursoController::class, 'destroy']);

        // Clases
        Route::post('/cursos/{curso}/clases', [ClaseController::class, 'store']);
        Route::patch('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'update']);
        Route::delete('/cursos/{curso}/clases/{clase}', [ClaseController::class, 'destroy']);

        // Formularios
        Route::get('/cursos/{curso}/formularios', [FormularioInscripcionController::class, 'index']);
        Route::post('/cursos/{curso}/formularios', [FormularioInscripcionController::class, 'store']);
        Route::patch('/formularios/{formulario}/toggle-activo', [FormularioInscripcionController::class, 'toggleActivo']);

        // Estudiantes
        Route::patch('/estudiantes/{registroEstudiante}/estado', [RegistroEstudianteController::class, 'cambiarEstado']);

        // Asistencia
        Route::post('/clases/{clase}/asistencia', [AsistenciaController::class, 'store']);

        // Calificaciones
        Route::post('/clases/{clase}/calificaciones', [CalificacionController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | SOLO COORDINADOR (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:coordinador')->group(function () {

        // Usuarios
        Route::get('/usuarios', [UserController::class, 'index']);
        Route::post('/usuarios', [UserController::class, 'store']);
        Route::get('/usuarios/{user}', [UserController::class, 'show']);
        Route::patch('/usuarios/{user}/toggle-activo', [UserController::class, 'toggleActivo']);

        // Regionales (CRUD completo)
        Route::post('/regionales', [RegionalController::class, 'store']);
        Route::patch('/regionales/{regional}', [RegionalController::class, 'update']);
        Route::delete('/regionales/{regional}', [RegionalController::class, 'destroy']);

        // Reportes
        Route::get('/reportes/cursos', [ReporteController::class, 'resumenCursos']);
        Route::get('/reportes/cursos/{curso}/pdf', [ReporteController::class, 'cursoPdf']);
        Route::get('/reportes/cursos/{curso}/asistencia-pdf', [ReporteController::class, 'asistenciaPdf']);
        Route::get('/reportes/cursos/{curso}/calificaciones-pdf', [ReporteController::class, 'calificacionesPdf']);
    });

    /*
    |--------------------------------------------------------------------------
    | SOLO ESTUDIANTE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:estudiante')->group(function () {

        Route::get('/mi-progreso', function (Illuminate\Http\Request $request) {
            $user = $request->user();

            return \App\Models\RegistroEstudiante::with('curso.regional')
                ->where('user_id', $user->id)
                ->get();
        });
    });
});