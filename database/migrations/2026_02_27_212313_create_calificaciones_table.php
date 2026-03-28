<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clase_id')->constrained('clases')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('users')->cascadeOnDelete();
            // Nota de 0.0 a 5.0
            $table->decimal('nota', 3, 1);
            $table->text('observacion')->nullable();
            // TODO: si en el futuro se quieren múltiples actividades por clase,
            // agregar campo: string 'actividad' y ajustar el promedio en el modelo
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('calificaciones');
    }
};