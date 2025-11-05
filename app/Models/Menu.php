<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'precio',
        'categoria',
        'disponible',
        'imagen_url',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_menu', 'id_menu');
    }
}
