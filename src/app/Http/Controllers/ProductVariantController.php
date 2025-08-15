<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariantRequest;
use App\Models\ProductVariant;

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
}
