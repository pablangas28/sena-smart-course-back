<?php

namespace Database\Seeders;

use App\Models\Regional;
use Illuminate\Database\Seeder;

class RegionalSeeder extends Seeder
{
    public function run(): void
    {
        $regionales = [
            ['nombre' => 'Alto Occidente', 'departamento' => 'Caldas'],
            ['nombre' => 'Alto Oriente',   'departamento' => 'Caldas'],
            ['nombre' => 'Occidente Próspero', 'departamento' => 'Caldas'],
            ['nombre' => 'Bajo Occidente', 'departamento' => 'Caldas'],
        ];

        foreach ($regionales as $regional) {
            Regional::firstOrCreate(
                ['nombre' => $regional['nombre']],
                $regional
            );
        }
    }
}