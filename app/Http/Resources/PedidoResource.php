<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_pedido' => $this->id_pedido,
            'id_cliente' => $this->id_cliente,
            'id_empleado' => $this->id_empleado,
            'id_estado' => $this->id_estado,
            'numero_mesa' => $this->numero_mesa,
            'fecha_creacion' => optional($this->fecha_creacion)->toISOString(),
            'total' => isset($this->total) ? (float) $this->total : null,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'empleado' => new EmpleadoResource($this->whenLoaded('empleado')),
            'estado' => new EstadoPedidoResource($this->whenLoaded('estado')),
            'detalles' => DetallePedidoResource::collection($this->whenLoaded('detalles')),
        ];
    }
}
