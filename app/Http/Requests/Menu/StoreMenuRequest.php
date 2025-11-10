<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autenticación manejada por Sanctum y middleware
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'categoria' => ['required', 'string', 'max:100'],
            'disponible' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser numérico.',
            'precio.min' => 'El precio debe ser mayor o igual a 0.',
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.max' => 'La categoría no puede exceder 100 caracteres.',
            'disponible.boolean' => 'El campo disponible debe ser verdadero o falso.',
        ];
    }
}
