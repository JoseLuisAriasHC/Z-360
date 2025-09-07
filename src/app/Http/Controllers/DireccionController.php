<?php

namespace App\Http\Controllers;

use App\Http\Requests\DireccionRequest;
use App\Models\Direccion;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    public function index()
    {
        $direcciones = Direccion::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' => $direcciones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DireccionRequest $request)
    {
        $predeterminada = $request->boolean('predeterminada', false);
        if ($predeterminada) {
            Direccion::where('user_id', Auth::id())->update(['predeterminada' => false]);
        }

        $direccion = Direccion::create(array_merge(
            $request->validated(),
            [
                'user_id' => Auth::id(),
                'predeterminada' => $predeterminada
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Dirección creada correctamente',
            'data' => $direccion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Direccion $direccion)
    {
        if ($direccion->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver esta dirección'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $direccion
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DireccionRequest $request, Direccion $direccion)
    {
        if ($direccion->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar esta dirección'
            ], 403);
        }

        $predeterminada = $request->boolean('predeterminada', false);
        if ($predeterminada) {
            Direccion::where('user_id', Auth::id())->update(['predeterminada' => false]);
        }

        $direccion->update(array_merge(
            $request->validated(),
            ['predeterminada' => $predeterminada]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada correctamente',
            'data' => $direccion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Direccion $direccion)
    {
        if ($direccion->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta dirección'
            ], 403);
        }

        $direccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada correctamente'
        ]);
    }
}
