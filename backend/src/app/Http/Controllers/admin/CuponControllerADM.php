<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CuponRequest;
use App\Models\Cupon;
use Illuminate\Http\Request;

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

    /**
     * Eliminar múltiples cupones.
     * Recibe un array de IDs y los elimina de forma eficiente.
     */
    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:cupones,id',
        ]);

        $ids = $validated['ids'];
        $count = Cupon::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => "{$count} cupones eliminadas correctamente."
        ]);
    }
}
