<?php

namespace App\Http\Requests\Pedido;

use Illuminate\Foundation\Http\FormRequest;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => ['required', 'integer', 'exists:clientes,id_cliente'],
            'id_empleado' => ['required', 'integer', 'exists:empleados,id_empleado'],
            'numero_mesa' => ['nullable', 'string', 'max:50'],
            'id_estado' => ['sometimes', 'integer', 'exists:estado_pedido,id_estado'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.id_menu' => ['required', 'integer', 'exists:menu,id_menu'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.required' => 'El cliente es obligatorio.',
            'id_cliente.exists' => 'El cliente seleccionado no existe.',
            'id_empleado.required' => 'El empleado es obligatorio.',
            'id_empleado.exists' => 'El empleado seleccionado no existe.',
            'numero_mesa.max' => 'El número de mesa no puede exceder 50 caracteres.',
            'id_estado.exists' => 'El estado indicado no existe.',

            'detalles.required' => 'Debes enviar al menos un detalle del pedido.',
            'detalles.array' => 'El campo detalles debe ser un arreglo.',
            'detalles.min' => 'Debes incluir al menos un producto en los detalles.',

            'detalles.*.id_menu.required' => 'El producto (id_menu) es obligatorio en cada detalle.',
            'detalles.*.id_menu.exists' => 'Algún producto indicado en los detalles no existe.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria en cada detalle.',
            'detalles.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'detalles.*.precio_unitario.required' => 'El precio unitario es obligatorio en cada detalle.',
            'detalles.*.precio_unitario.numeric' => 'El precio unitario debe ser numérico.',
            'detalles.*.precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0.',
        ];
    }
}
