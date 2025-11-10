<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoPedidoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_estado' => $this->id_estado,
            'nombre_estado' => $this->nombre_estado,
        ];
    }
}
