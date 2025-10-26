<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
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
            'precio'             => ['required', 'numeric', 'min:0'],
            'descuento'          => ['nullable', 'numeric', 'min:0'],
            'descuento_desde'    => ['nullable', 'date'],
            'descuento_hasta'    => ['nullable', 'date', 'after_or_equal:descuento_desde'],
            'imagen_principal.*' => ['sometimes', 'file', 'mimes:avif,jpeg,jpg,png,webp'],
        ];
    }
}
