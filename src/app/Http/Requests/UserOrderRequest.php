<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserOrderRequest extends FormRequest
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
            // cupón y pago
            'cupon_codigo'  => ['nullable', 'string', 'max:50'],
            'metodo_pago'   => ['required', Rule::in(['tarjeta', 'paypal', 'otro'])],
            'costo_envio'   => ['nullable', 'numeric', 'min:0'],

            // datos del cliente 
            'nombre_cliente'   => ['required', 'string', 'max:150'],
            'email_cliente'    => ['nullable', 'email', 'max:150'],
            'telefono_cliente' => ['nullable', 'string', 'max:30'],

            // dirección
            'direccion_calle'        => ['nullable', 'string', 'max:100'],
            'direccion_numero_calle' => ['nullable', 'string', 'max:10'],
            'direccion_piso_info'    => ['nullable', 'string', 'max:50'],
            'direccion_ciudad'       => ['nullable', 'string', 'max:100'],
            'direccion_cp'           => ['nullable', 'string', 'max:10'],
        ];
    }
}
