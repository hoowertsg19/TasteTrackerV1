<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_empleado' => $this->id_empleado,
            'nombre_completo' => $this->nombre_completo,
            'rol' => $this->rol,
            'activo' => (bool) $this->activo,
        ];
    }
}
