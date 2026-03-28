<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoordinadorSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'coordinador@sena.edu.co'],
            [
                'nombre'    => 'Coordinador',
                'apellidos' => 'SENA',
                'email'     => 'coordinador@sena.edu.co',
                'password'  => Hash::make('Sena2025*'),
                'rol'       => 'coordinador',
                'activo'    => true,
            ]
        );
    }
}