<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
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
        $variants = ProductVariant::with(['product', 'color', 'sizes', 'images'])
            ->paginate(config('web.paginacion_por_pagina'));

        return response()->json([
            'success' => true,
            'data' => $variants,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductVariantRequest $request)
    {
        $variant = ProductVariant::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variante de producto creada correctamente',
            'data' => $variant,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        $productVariant->load(['product', 'color', 'sizes', 'images']);

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

        $variants = [];

        DB::transaction(function () use ($productId, $colores, $tallas, &$variants) {
            foreach ($colores as $colorId) {
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

                $variants[] = $variant->load('sizes');
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Combinaciones generadas correctamente',
            'data'    => $variants,
        ]);
    }
}
