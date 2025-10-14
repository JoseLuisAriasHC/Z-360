<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUsage;
use Illuminate\Http\Request;

class ProductUsageControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usages = ProductUsage::paginate(config('web.paginacion_por_pagina'));
        return response()->json([
            'success' => true,
            'data' => $usages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:product_usages,nombre'
        ]);

        $usage = ProductUsage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Uso creado correctamente',
            'data' => $usage
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductUsage $usage)
    {
        return response()->json([
            'success' => true,
            'data' => $usage
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductUsage $usage)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:product_usages,nombre,' . $usage->id
        ]);

        $usage = ProductUsage::update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Uso actulizaddo correctamente',
            'data' => $usage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductUsage $usage)
    {
        $usage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Uso eliminado correctamente'
        ]);
    }

    /**
     * Asignar usos a un producto
     */
    public function asignarUsos(Request $request, Product $product)
    {
        $validated = $request->validate([
            'usages' => 'required|array',
            'usages.*' => 'exists:product_usages,id'
        ]);

        $product->usages()->sync($validated['usages']);

        return response()->json([
            'success' => true,
            'message' => 'Usos asignados correctamente',
            'data' => $product->load('usages')
        ]);
    }

    /**
     * Mostrar usos de un producto
     */
    public function showProductUsages(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->usages
        ]);
    }
}
