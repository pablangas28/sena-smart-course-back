<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $fillable = [
        'clase_id',
        'estudiante_id',
        'nota',
        'observacion',
        // TODO: si en el futuro se quieren múltiples actividades por clase,
        // agregar campo 'actividad' y ajustar el promedio en el modelo
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:1',
        ];
    }

    public function clase()
    {
        return $this->belongsTo(Clase::class);
    }

    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }
}