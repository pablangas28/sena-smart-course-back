<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Curso;
use App\Models\Regional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener una regional existente
        $regional = Regional::first();

        // Crear instructor de prueba
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@sena.edu.co'],
            [
                'nombre'      => 'José',
                'apellidos'   => 'Germán Estrada',
                'email'       => 'instructor@sena.edu.co',
                'password'    => Hash::make('Sena2025*'),
                'rol'         => 'instructor',
                'telefono'    => '3200689554',
                'ocupacion'   => 'Instructor SENA',
                'regional_id' => $regional->id,
                'activo'      => true,
            ]
        );

        // Crear aliado de prueba
        $aliado = User::firstOrCreate(
            ['email' => 'aliado@sena.edu.co'],
            [
                'nombre'      => 'María',
                'apellidos'   => 'González López',
                'email'       => 'aliado@sena.edu.co',
                'password'    => Hash::make('Sena2025*'),
                'rol'         => 'aliado',
                'telefono'    => '3004276317',
                'ocupacion'   => 'Coordinadora Aliada',
                'regional_id' => $regional->id,
                'activo'      => true,
            ]
        );

        // Crear curso de prueba
        $curso = Curso::firstOrCreate(
            ['nombre' => 'Docencia Universitaria'],
            [
                'nombre'           => 'Docencia Universitaria',
                'descripcion'      => 'Curso complementario de docencia universitaria para instructores SENA',
                'creado_por'       => $instructor->id,
                'regional_id'      => $regional->id,
                'horas_requeridas' => 40,
                'fecha_inicio'     => now(),
                'fecha_fin'        => now()->addMonths(3),
                'estado'           => 'activo',
            ]
        );

        $this->command->info('✅ Instructor creado: instructor@sena.edu.co');
        $this->command->info('✅ Aliado creado: aliado@sena.edu.co');
        $this->command->info('✅ Curso creado: Docencia Universitaria');
        $this->command->info('🔑 Password de todos: Sena2025*');
    }
}