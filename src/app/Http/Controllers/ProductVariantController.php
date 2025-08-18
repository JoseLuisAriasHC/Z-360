<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variants = ProductVariant::with(['color', 'sizes'])
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        $productVariant->load(['color', 'sizes', 'images']);

        return response()->json([
            'success' => true,
            'data' => $productVariant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductVariantRequest $request, ProductVariant $productVariant)
    {
        $productVariant->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variante de producto actualizada correctamente',
            'data' => $productVariant,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variante de producto eliminada correctamente',
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'colores'    => 'required|array|min:1',
            'colores.*'  => 'exists:colores,id',
            'tallas'     => 'required|array|min:1',
            'tallas.*'   => 'exists:tallas,id',
        ]);

        $productId = $request->input('product_id');
        $colores   = $request->input('colores');
        $tallas    = $request->input('tallas');

        DB::transaction(function () use ($productId, $colores, $tallas) {
            foreach ($colores as $colorId) {
                $existVariant = ProductVariant::where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->first();
                if ($existVariant) {
                    continue;
                }

                $variant = ProductVariant::create([
                    'product_id'        => $productId,
                    'color_id'          => $colorId,
                    'precio'            => 0,
                    'descuento'         => null,
                    'imagen_principal'  => null,
                ]);

                foreach ($tallas as $tallaId) {
                    $variant->sizes()->create([
                        'talla_id'   => $tallaId,
                        'stock'      => 0,
                        'sku'        => strtoupper("SKU-{$variant->id}-{$tallaId}"),
                    ]);
                }
            }
        });

        $variants = ProductVariant::with(['color', 'sizes.talla'])
            ->paginate(config('web.paginacion_por_pagina'));

        return ProductVariantResource::collection($variants);
    }
}
