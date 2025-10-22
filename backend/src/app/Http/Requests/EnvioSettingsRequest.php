<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnvioSettingsRequest extends FormRequest
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
        // Los costes deben ser numéricos y mayores o iguales a cero.
        return [
            'coste_envio'           => ['required', 'numeric', 'min:0'],
            'free_coste_envio_from' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Nombres amigables para los atributos en los mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'coste_envio'           => 'Coste de Envío',
            'free_coste_envio_from' => 'Mínimo para Envío Gratuito',
        ];
    }
}
