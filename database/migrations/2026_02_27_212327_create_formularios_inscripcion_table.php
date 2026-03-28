<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('formularios_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('creado_por')->constrained('users')->cascadeOnDelete();
            // Link único que se comparte con los estudiantes
            $table->string('token')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamp('expira_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('formularios_inscripcion');
    }
};