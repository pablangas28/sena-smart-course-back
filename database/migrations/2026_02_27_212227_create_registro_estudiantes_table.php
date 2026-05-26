<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registro_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['masculino', 'femenino', 'otro']);
            $table->string('telefono')->nullable();
            $table->string('celular');
            $table->string('documento');
            $table->string('cel_contacto_emergencia');
            $table->string('pantallazo_sofia')->nullable(); // ruta del archivo subido
            // Estados: activo, desertado, graduado
            $table->enum('estado', ['activo', 'desertado', 'graduado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('registro_estudiantes');
    }
};