<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariantImageRequest;
use App\Models\VariantImage;

class VariantImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = VariantImage::with('variant')
            ->paginate(config('web.paginacion_por_pagina'));

        return response()->json([
            'success' => true,
            'data' => $images,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantImageRequest $request)
    {
        $image = VariantImage::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Imagen de variante creada correctamente',
            'data' => $image,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(VariantImage $variantImage)
    {
        $variantImage->load('variant');

        return response()->json([
            'success' => true,
            'data' => $variantImage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantImageRequest $request, VariantImage $variantImage)
    {
        $variantImage->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Imagen de variante actualizada correctamente',
            'data' => $variantImage,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariantImage $variantImage)
    {
        $variantImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen de variante eliminada correctamente',
        ]);
    }
}
