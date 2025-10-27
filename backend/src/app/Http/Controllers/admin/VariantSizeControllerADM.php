<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\VariantSize;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariantSizeControllerADM extends Controller
{
    /**
     * Obtener las tallas que tiene el producto variante
     */
    public function getVariantSizes(ProductVariant $productVariant)
    {
        $productVariant->load('sizes.talla');
        return response()->json([
            'success' => true,
            'data' => $productVariant->sizes,
        ]);
    }

    /**
     * Obtener las tallas que tiene el producto variante
     */
    public function updateVarianteSize(Request $request, VariantSize $variantSize)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $stock = $validated['stock'];

        $variantSize->update([
            'stock' => $stock
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Talla de la variante actulizadda correctamente',
            'data' => $variantSize
        ]);
    }

    public function createVarianteSize(Request $request, ProductVariant $productVariant)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'talla_id' => 'required|integer|exists:tallas,id',
            Rule::unique('variant_sizes', 'talla_id')
                ->where(function ($query) use ($productVariant) {
                    return $query->where('product_variant_id', $productVariant->id);
                })
        ]);

        $variantSize = $productVariant->sizes()->create($validated);
        $variantSize->load('talla');

        return response()->json([
            'success' => true,
            'message' => 'Talla de la variante creada correctamente',
            'data' => $variantSize
        ], 201);
    }

    public function destroy(VariantSize $variantSize)
    {
        $variantSize->delete();

        return response()->json([
            'success' => true,
            'message' => 'Talla de la variante eliminada correctamente'
        ]);
    }
}
