<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_cliente.required' => 'El nombre del cliente es obligatorio.',
            'nombre_cliente.max' => 'El nombre del cliente no puede exceder 255 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder 30 caracteres.',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres.',
        ];
    }
}
