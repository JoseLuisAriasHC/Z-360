<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebSettingsRequest extends FormRequest
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
        $clave = $this->route('clave');

        $rules = [
            'valor'       => ['required', 'string', 'max:255'],
            'nombre'      => ['sometimes', 'string', 'max:255'],
            'descripcion' => ['sometimes', 'string'],
        ];

        switch ($clave) {
            case 'max_fotos_producto_por_usuario':
                $rules['valor'] = ['required', 'integer', 'min:0'];
                break;
        }

        return $rules;
    }
}
