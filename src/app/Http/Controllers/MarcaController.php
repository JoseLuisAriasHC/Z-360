<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Listar todas las marcas.
     */
    public function index()
    {
        $marcas = Marca::paginate(config('web.paginacion_por_pagina'));

        return response()->json([
            'success' => true,
            'data' => $marcas
        ]);
    }

    /**
     * Guardar una nueva marca.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre',
            'logo_url' => 'nullable|url|max:255',
        ]);

        $marca = Marca::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Marca creada correctamente',
            'data' => $marca
        ], 201);
    }

    /**
     * Mostrar una marca específica.
     */
    public function show(Marca $marca)
    {
        return response()->json([
            'success' => true,
            'data' => $marca
        ]);
    }

    /**
     * Actualizar una marca.
     */
    public function update(Request $request, Marca $marca)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $marca->id,
            'logo_url' => 'nullable|url|max:255',
        ]);

        $marca->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Marca actualizada correctamente',
            'data' => $marca
        ]);
    }

    /**
     * Eliminar una marca.
     */
    public function destroy(Marca $marca)
    {
        $marca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada correctamente'
        ]);
    }
}
