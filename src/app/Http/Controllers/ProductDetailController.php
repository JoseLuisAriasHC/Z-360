<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->detail
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'parte_superior' => 'nullable|string|max:255',
            'parte_inferior' => 'nullable|string|max:255',
            'suela' => 'nullable|string|max:255',
            'cuidados' => 'nullable|string',
        ]);

        $product->detail()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Detalles del producto actualizados correctamente',
            'data' => $product->detail
        ]);
    }
}
