<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_cliente' => $this->id_cliente,
            'nombre_cliente' => $this->nombre_cliente,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
        ];
    }
}
