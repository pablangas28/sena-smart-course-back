<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormularioInscripcion extends Model
{
    protected $table = 'formularios_inscripcion';

    protected $fillable = [
        'curso_id',
        'creado_por',
        'token',
        'activo',
        'expira_en',
    ];

    protected function casts(): array
    {
        return [
            'activo'    => 'boolean',
            'expira_en' => 'datetime',
        ];
    }

    // Genera token único automáticamente al crear
    protected static function booted(): void
    {
        static::creating(function ($formulario) {
            $formulario->token = Str::uuid();
        });
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    // Verifica si el formulario sigue vigente
    public function estaVigente(): bool
    {
        if (!$this->activo) return false;
        if ($this->expira_en && now()->isAfter($this->expira_en)) return false;
        return true;
    }
}