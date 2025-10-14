<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use App\Models\Product;
use Illuminate\Http\Request;

class EtiquetaControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $etiquetas = Etiqueta::paginate(config('web.paginacion_por_pagina'));
        return response()->json([
            'success' => true,
            'data' => $etiquetas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:etiquetas,nombre',
        ]);

        $etiqueta = Etiqueta::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta creada correctamente',
            'data' => $etiqueta
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiqueta $etiqueta)
    {
        return response()->json([
            'success' => true,
            'data' => $etiqueta
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etiqueta $etiqueta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:etiquetas,nombre,' . $etiqueta->id,
        ]);

        $etiqueta->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta actualizada correctamente',
            'data' => $etiqueta
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiqueta $etiqueta)
    {
        $etiqueta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta eliminada correctamente'
        ]);
    }

    public function asignarEtiquetas(Request $request, Product $product)
    {
        $request->validate([
            'etiquetas' => 'required|array',
            'etiquetas.*' => 'exists:etiquetas,id',
        ]);

        // Agrega las etiquetas pasadas al producto, sin eliminar las que ya tiene, y sin duplicar las existentes
        $product->etiquetas()->syncWithoutDetaching($request->etiquetas);

        return response()->json([
            'success' => true,
            'message' => 'Etiquetas asignadas correctamente',
            'data' => $product->load('etiquetas')
        ]);
    }

    public function eliminarEtiquetas(Request $request, Product $product)
    {
        $request->validate([
            'etiquetas' => 'required|array',
            'etiquetas.*' => 'exists:etiquetas,id',
        ]);

        $product->etiquetas()->detach($request->etiquetas);

        return response()->json([
            'success' => true,
            'message' => 'Etiquetas eliminadas correctamente',
            'data' => $product->load('etiquetas')
        ]);
    }

    public function limpiarEtiqueta(Etiqueta $etiqueta)
    {
        $etiqueta->products()->detach();

        return response()->json([
            'success' => true,
            'message' => "Etiqueta '{$etiqueta->nombre}' eliminada de todos los productos"
        ]);
    }
}
