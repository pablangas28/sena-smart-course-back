<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroEstudiante extends Model
{
    protected $fillable = [
        'user_id',
        'curso_id',
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'celular',
        'documento',
        'cel_contacto_emergencia',
        'pantallazo_sofia',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}