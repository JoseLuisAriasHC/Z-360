<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarcaRequest;
use App\Models\Marca;
use Illuminate\Support\Facades\Storage;

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
    public function store(MarcaRequest $request)
    {
        $validated = $request->validated();
        $log_path = null;

        if ($request->hasFile('logo')) {
            $log_path = $request->file('logo')->store('marca_logos', 'public');
        }

        $marca = Marca::create([
            'nombre'       => $validated['nombre'],
            'talla_offset' => $validated['talla_offset'],
            'logo'     => $log_path
        ]);

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
    public function update(MarcaRequest $request, Marca $marca)
    {
        $request->validated();

        if ($request->hasFile('logo') && $marca->logo && Storage::disk('public')->exists($marca->logo))
            Storage::disk('public')->delete($marca->logo);

        $marca->update([
            'nombre'        => $request->nombre ?? $marca->nombre,
            'talla_offset'  => $request->talla_offset ?? $marca->talla_offset,
            'logo'          => $request->hasFile('logo') ? $request->file('logo')->store('marca_logos', 'public') : $marca->logo,
        ]);

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
