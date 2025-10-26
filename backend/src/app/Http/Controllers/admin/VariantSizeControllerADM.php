<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class VariantSizeController extends Controller
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
}
