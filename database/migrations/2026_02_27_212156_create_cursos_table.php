<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('creado_por')->constrained('users')->cascadeOnDelete();
            $table->foreignId('regional_id')->constrained('regionales')->cascadeOnDelete();
            $table->integer('horas_requeridas')->default(40);
            $table->integer('horas_cumplidas')->default(0);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            // Estados: activo, finalizado, cancelado
            $table->enum('estado', ['activo', 'finalizado', 'cancelado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cursos');
    }
};