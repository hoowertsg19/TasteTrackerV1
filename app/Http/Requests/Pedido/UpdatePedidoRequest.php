<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_estado' => ['required', 'integer', 'exists:estado_pedido,id_estado'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_estado.required' => 'El estado es obligatorio.',
            'id_estado.exists' => 'El estado indicado no existe.',
        ];
    }
}
