<?php

namespace App\Http\Requests\Empleado;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => ['sometimes', 'required', 'string', 'max:255'],
            'rol' => ['sometimes', 'required', 'string', 'max:100'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es obligatorio cuando se envía.',
            'nombre_completo.max' => 'El nombre completo no puede exceder 255 caracteres.',
            'rol.required' => 'El rol es obligatorio cuando se envía.',
            'rol.max' => 'El rol no puede exceder 100 caracteres.',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso.',
        ];
    }
}
