<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaSettingsRequest extends FormRequest
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
        // Las claves de la base de datos se validan como parte del cuerpo de la petición.
        return [
            'empresa_nombre'    => ['required', 'string', 'max:255'],
            'empresa_direccion' => ['required', 'string', 'max:500'],
            'empresa_telefono'  => ['nullable', 'string', 'max:50'],
            'empresa_email'     => ['required', 'email', 'max:255'],
        ];
    }

    /**
     * Nombres amigables para los atributos en los mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'empresa_nombre'    => 'Nombre de la Empresa',
            'empresa_direccion' => 'Dirección de la Empresa',
            'empresa_telefono'  => 'Teléfono de la Empresa',
            'empresa_email'     => 'Correo de la Empresa',
        ];
    }
}
