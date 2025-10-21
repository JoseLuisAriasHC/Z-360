<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ColorRequest extends FormRequest
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
        $IdToIgnore = optional($this->route('color'))->id;
        return [
            'nombre'     => ['required', 'string', 'max:100', Rule::unique('colores', 'nombre')->ignore($IdToIgnore)],
            'codigo_hex' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
        ];
    }
}
