<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetallePedidoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_detalle' => $this->id_detalle,
            'id_menu' => $this->id_menu,
            'cantidad' => (int) $this->cantidad,
            'precio_unitario' => isset($this->precio_unitario) ? (float) $this->precio_unitario : null,
            'subtotal' => isset($this->subtotal) ? (float) $this->subtotal : null,
            'menu' => new MenuResource($this->whenLoaded('menu')),
        ];
    }
}
