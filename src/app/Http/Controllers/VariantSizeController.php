<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariantSizeRequest;
use App\Models\VariantSize;

class VariantSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = VariantSize::with('productVariant.color', 'talla')->paginate(config('web.paginacion_por_pagina'));

        return response()->json([
            'success' => true,
            'data' => $sizes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantSizeRequest $request)
    {
        $size = VariantSize::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto variante de talla creado correctamente',
            'data' => $size,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(VariantSize $variantSize)
    {
        $variantSize->load('variant', 'talla');

        return response()->json([
            'success' => true,
            'data' => $variantSize,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantSizeRequest $request, VariantSize $variantSize)
    {
        $variantSize->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto variante de talla actualizado correctamente',
            'data' => $variantSize,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariantSize $variantSize)
    {
        $variantSize->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto variante de talla eliminado correctamente',
        ]);
    }
}
