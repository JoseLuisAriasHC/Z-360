<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'nombre'        => ['required', 'string', 'max:255'],
            'marca_id'      => ['required', 'exists:marcas,id'],
            'tipo'          => ['required', Rule::in(['urbanas', 'deportivas', 'botas', 'sandalias'])],
            'descripcion'   => ['nullable', 'string'],
            'cierre'        => ['required', Rule::in(['Cordones', 'Velcro', 'Cierre rapido', 'Zipper', 'Slip-on'])],
            'altura_suela'  => ['required', Rule::in(['baja', 'media', 'alta'])],
            'plantilla'     => ['nullable', 'string', 'max:100'],
            'genero'        => ['required', Rule::in(['hombre', 'mujer', 'unisex'])],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.in'          => 'El tipo solo puede ser urbanas, deportivas, botas o sandalias',
            'cierre.in'        => 'El cierre solo puede ser Cordones, Velcro, Cierre rapido, Zipper o Slip-on',
            'altura_suela.in'  => 'La altura de la suela solo puede ser baja, media o alta',
            'genero.in'        => 'El genero solo puede ser hombre, mujer o unisex',
        ];
    }
}
