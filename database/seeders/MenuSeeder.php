<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'nombre' => 'Hamburguesa Clásica',
                'precio' => 120.00,
                'categoria' => 'comidas',
                'disponible' => true,
            ],
            [
                'nombre' => 'Ensalada César',
                'precio' => 95.50,
                'categoria' => 'comidas',
                'disponible' => true,
            ],
            [
                'nombre' => 'Limonada Natural',
                'precio' => 35.00,
                'categoria' => 'bebidas',
                'disponible' => true,
            ],
            [
                'nombre' => 'Café Americano',
                'precio' => 28.00,
                'categoria' => 'bebidas',
                'disponible' => true,
            ],
            [
                'nombre' => 'Pastel de Chocolate',
                'precio' => 65.00,
                'categoria' => 'postres',
                'disponible' => true,
            ],
        ];

        foreach ($items as $item) {
            Menu::create($item);
        }
    }
}
