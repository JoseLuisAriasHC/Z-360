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
            // Datos de ENVÍO
            'envio_nombre'                  => ['required', 'string', 'max:255'],
            'envio_email'                   => ['required', 'email', 'max:255'],
            'envio_telefono'                => ['nullable', 'string', 'regex:/^6\d{8}$|^7\d{8}$/'],
            'envio_direccion_calle'         => ['required', 'string', 'max:255'],
            'envio_direccion_numero_calle'  => ['required', 'string', 'max:50'],
            'envio_direccion_piso_info'     => ['nullable', 'string', 'max:255'],
            'envio_direccion_ciudad'        => ['required', 'string', 'max:100'],
            'envio_direccion_cp'            => ['required', 'string', 'max:10'],

            'usar_misma_direccion_facturacion' => ['boolean'],

            // Datos de FACTURACIÓN
            'facturacion_nombre'                    => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'max:255'],
            'facturacion_email'                     => ['required_if:usar_misma_direccion_facturacion,false', 'email', 'max:255'],
            'facturacion_telefono'                  => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'regex:/^6\d{8}$|^7\d{8}$/'],
            'facturacion_direccion_calle'           => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'max:255'],
            'facturacion_direccion_numero_calle'    => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'max:50'],
            'facturacion_direccion_piso_info'       => ['nullable', 'string', 'max:255'],
            'facturacion_direccion_ciudad'          => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'max:100'],
            'facturacion_direccion_cp'              => ['required_if:usar_misma_direccion_facturacion,false', 'string', 'max:10'],

            // cupón
            'cupon_codigo'  => ['nullable', 'string', 'max:50'],
        ];
    }
}
