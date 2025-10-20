<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TallaRequest;
use App\Models\Talla;

class TallaControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tallas = Talla::all();
        return response()->json([
            'success' => true,
            'data' => $tallas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TallaRequest $request)
    {
        $talla = Talla::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Talla creada correctamente',
            'data' => $talla
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Talla $talla)
    {
        return response()->json([
            'success' => true,
            'data' => $talla
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TallaRequest $request, Talla $talla)
    {
        $talla->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Talla actulizadda correctamente',
            'data' => $talla
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Talla $talla)
    {
        $talla->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada correctamente'
        ]);
    }
}
