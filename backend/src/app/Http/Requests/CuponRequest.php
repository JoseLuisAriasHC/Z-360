<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CuponRequest extends FormRequest
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
        $IdToIgnore = optional($this->route('cupon'))->id;
        return [
            'codigo'           => ['required', 'string', 'max:50', Rule::unique('cupones', 'codigo')->ignore($IdToIgnore)],
            'descuento'        => ['required', 'numeric', 'min:0'],
            'tipo'             => ['required', Rule::in(['porcentaje', 'fijo'])],
            'fecha_expiracion' => ['required', 'date', 'after:today'],
            'uso_maximo'       => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique'            => 'Este código ya existe.',
            'fecha_expiracion.after'   => 'La fecha de expiración debe ser futura.',
        ];
    }
}
