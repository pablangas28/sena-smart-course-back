<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    protected $table = 'regionales'; // 👈 esto le dice a Laravel el nombre exacto

    protected $fillable = [
        'nombre',
        'departamento',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}