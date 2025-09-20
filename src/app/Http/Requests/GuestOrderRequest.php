<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestOrderRequest extends FormRequest
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
            // cliente invitado con items del cliente
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.variant_size_id' => ['required', 'exists:variant_sizes,id'],
            'items.*.cantidad'        => ['required', 'integer', 'min:1'],

            // datos del cliente 
            'nombre_cliente'    => ['required', 'string', 'max:150'],
            'email_cliente'     => ['required', 'email', 'max:150'],
            'telefono_cliente'  => ['required', 'string', 'max:30'],

            // dirección
            'direccion_calle'         => ['required', 'string', 'max:100'],
            'direccion_numero_calle'  => ['required', 'string', 'max:10'],
            'direccion_piso_info'     => ['nullable', 'string', 'max:50'],
            'direccion_ciudad'        => ['required', 'string', 'max:100'],
            'direccion_cp'            => ['required', 'string', 'max:10'],

            // otros 
            'cupon_codigo'  => ['nullable', 'string', 'max:50'],
            'metodo_pago'   => ['required', Rule::in(['tarjeta', 'paypal', 'otro'])],
        ];
    }
}
