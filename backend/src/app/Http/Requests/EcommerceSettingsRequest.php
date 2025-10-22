<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EcommerceSettingsRequest extends FormRequest
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
            'iva'                            => ['required', 'numeric', 'min:0', 'max:100'],
            'email_admin'                    => ['required', 'email', 'max:255'],
            'max_fotos_producto_por_usuario' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Nombres amigables para los atributos en los mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'iva'                            => 'IVA',
            'email_admin'                    => 'Correo del Administrador',
            'max_fotos_producto_por_usuario' => 'Máximo de Fotos por Producto',
        ];
    }
}
