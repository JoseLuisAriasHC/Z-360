<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantSizeRequest extends FormRequest
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
            'variant_id' => 'required|exists:product_variants,id',
            'talla_id'   => 'required|exists:tallas,id',
            'stock'      => 'required|integer|min:0',
            'sku'        => 'required|string|max:50|unique:variant_sizes,sku,' . $this->route('variant_size'),
        ];
    }
}
