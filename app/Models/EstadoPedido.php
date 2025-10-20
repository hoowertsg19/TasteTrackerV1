<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model
{
    protected $table = 'estado_pedido';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'nombre_estado',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_estado', 'id_estado');
    }
}
