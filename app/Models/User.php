<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'ocupacion',
        'rol',
        'regional_id',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // Relaciones
    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function cursosCreados()
    {
        return $this->hasMany(Curso::class, 'creado_por');
    }

    public function registros()
    {
        return $this->hasMany(RegistroEstudiante::class, 'user_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'estudiante_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id');
    }

    // Helpers de rol
    public function esCoordinador(): bool
    {
        return $this->rol === 'coordinador';
    }

    public function esInstructor(): bool
    {
        return $this->rol === 'instructor';
    }

    public function esAliado(): bool
    {
        return $this->rol === 'aliado';
    }

    public function esEstudiante(): bool
    {
        return $this->rol === 'estudiante';
    }
}