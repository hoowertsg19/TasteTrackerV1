<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id_menu' => $this->id_menu,
            'nombre' => $this->nombre,
            'precio' => isset($this->precio) ? (float) $this->precio : null,
            'categoria' => $this->categoria,
            'disponible' => (bool) $this->disponible,
            'imagen_url' => $this->imagen_url,
        ];
    }
}
