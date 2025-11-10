<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre_cliente' => 'Ana Torres', 'telefono' => '555-1001', 'direccion' => 'Calle 1 #123'],
            ['nombre_cliente' => 'Bruno Díaz', 'telefono' => '555-1002', 'direccion' => 'Av. Central 45'],
            ['nombre_cliente' => 'Carla Gómez', 'telefono' => '555-1003', 'direccion' => 'Col. Centro s/n'],
            ['nombre_cliente' => 'Diego Ruiz', 'telefono' => null, 'direccion' => 'Zona Norte 12'],
            ['nombre_cliente' => 'Elena Márquez', 'telefono' => '555-1005', 'direccion' => null],
        ];

        foreach ($clientes as $c) {
            Cliente::updateOrCreate(
                ['nombre_cliente' => $c['nombre_cliente']],
                ['telefono' => $c['telefono'], 'direccion' => $c['direccion']]
            );
        }
    }
}
