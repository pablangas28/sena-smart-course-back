<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $fillable = [
        'curso_id',
        'tema',
        'fecha_hora',
        'tipo',
        'duracion_horas',
        // TODO: agregar más campos aquí si en el futuro se necesitan
        // por ejemplo: link_reunion, sala, materiales, etc.
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }
}