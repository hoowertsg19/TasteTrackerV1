<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EstadoPedido;

class EstadoPedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'Recibido',
            'En cocina',
            'Listo',
            'Pagado',
        ];

        foreach ($estados as $nombre) {
            EstadoPedido::firstOrCreate(['nombre_estado' => $nombre]);
        }
    }
}
