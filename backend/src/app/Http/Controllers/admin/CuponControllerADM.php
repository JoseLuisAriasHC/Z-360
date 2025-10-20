<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CuponRequest;
use App\Models\Cupon;

class CuponControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cupones = Cupon::all();
        return response()->json([
            'success' => true,
            'data' => $cupones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CuponRequest $request)
    {
        $cupon = Cupon::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cupón creado correctamente',
            'data' => $cupon
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cupon $cupon)
    {
        return response()->json([
            'success' => true,
            'data' => $cupon
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CuponRequest $request, Cupon $cupon)
    {
        $cupon->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cupón actualizado correctamente',
            'data' => $cupon
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cupon $cupon)
    {
        $cupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cupón eliminado correctamente'
        ]);
    }
}
