<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'creado_por',
        'regional_id',
        'horas_requeridas',
        'horas_cumplidas',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
        ];
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function clases()
    {
        return $this->hasMany(Clase::class);
    }

    public function formularios()
    {
        return $this->hasMany(FormularioInscripcion::class);
    }

    public function estudiantes()
    {
        return $this->hasMany(RegistroEstudiante::class);
    }

    // Calcula automáticamente las horas cumplidas sumando duración de clases
    public function actualizarHorasCumplidas(): void
    {
        $this->horas_cumplidas = $this->clases()->sum('duracion_horas');
        $this->save();
    }
}