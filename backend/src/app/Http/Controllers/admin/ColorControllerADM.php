<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ColorRequest;
use App\Models\Color;

class ColorControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colores = Color::all();
        return response()->json([
            'success' => true,
            'data' => $colores
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ColorRequest $request)
    {
        $color = Color::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Color creado correctamente',
            'data' => $color
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        return response()->json([
            'success' => true,
            'data' => $color
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ColorRequest $request, Color $color)
    {
        $color->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Color actualizado correctamente',
            'data' => $color
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        return response()->json([
            'success' => true,
            'message' => 'Color eliminado correctamente'
        ]);
    }
}
