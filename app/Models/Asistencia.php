<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'clase_id',
        'estudiante_id',
        'asistio',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'asistio' => 'boolean',
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