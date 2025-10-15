<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DireccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo'         => ['required', 'string', 'max:50'],
            'nombre'         => ['required', 'string', 'max:100'],
            'calle'          => ['required', 'string', 'max:100'],
            'numero_calle'   => ['required', 'string', 'max:10'],
            'piso_info'      => ['nullable', 'string', 'max:50'],
            'ciudad'         => ['required', 'string', 'max:100'],
            'cp'             => ['required', 'string', 'regex:/^\d{5}$/'],
            'telefono'       => ['required', 'string', 'regex:/^(?:\+34\s?)?([67]\d{2})(\s?\d{3})(\s?\d{3})$/'],
            'predeterminada' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'cp.regex'        => 'El código postal debe tener 5 dígitos, por ejemplo: 28001',
            'telefono.regex'  => 'El teléfono debe ser un número móvil español válido. Ejemplos: 600123456, 600 123 456, +34 600123456',
        ];
    }
}
