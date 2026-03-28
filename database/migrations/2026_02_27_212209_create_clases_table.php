<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->string('tema');
            $table->dateTime('fecha_hora');
            $table->enum('tipo', ['presencial', 'virtual']);
            $table->integer('duracion_horas')->default(2);
            // TODO: agregar más campos aquí si en el futuro se necesitan
            // por ejemplo: link_reunion, sala, materiales, etc.
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clases');
    }
};