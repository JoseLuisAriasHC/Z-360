<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TallaRequest extends FormRequest
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
            'numero' => [
                'required',
                'numeric',
                'min:0',
                'max:99.9',
                'unique:tallas,numero,' . $this->route('talla')->id,
                'regex:/^\d{1,2}(\.\d)?$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'numero.regex' => 'El número de talla solo puede tener un decimal, ejemplo: 36.5',
        ];
    }
}
