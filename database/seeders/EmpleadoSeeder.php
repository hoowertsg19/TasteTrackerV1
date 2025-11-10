<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empleado;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = [
            ['nombre_completo' => 'Juan Pérez', 'rol' => 'Mesero', 'activo' => true],
            ['nombre_completo' => 'Ana López', 'rol' => 'Cajera', 'activo' => true],
            ['nombre_completo' => 'Carlos García', 'rol' => 'Cocinero', 'activo' => true],
            ['nombre_completo' => 'María Fernández', 'rol' => 'Hostess', 'activo' => true],
            ['nombre_completo' => 'Luis Martínez', 'rol' => 'Barista', 'activo' => false],
        ];

        foreach ($empleados as $e) {
            Empleado::updateOrCreate(
                ['nombre_completo' => $e['nombre_completo'], 'rol' => $e['rol']],
                ['activo' => $e['activo']]
            );
        }
    }
}
