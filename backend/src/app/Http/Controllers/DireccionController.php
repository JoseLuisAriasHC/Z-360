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

    public function store(DireccionRequest $request)
    {
        $direccion = Auth::user()->direcciones()->create([
            ...$request->validated(),
            'predeterminada' => $request->boolean('predeterminada', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección creada correctamente',
            'data' => $direccion
        ], 201);
    }

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

    public function update(DireccionRequest $request, Direccion $direccion)
    {
        if ($direccion->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para actualizar esta dirección'
            ], 403);
        }

        $direccion->update([
            ...$request->validated(),
            'predeterminada' => $request->boolean('predeterminada', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada correctamente',
            'data' => $direccion
        ]);
    }

    public function destroy(Direccion $direccion)
    {
        if ($direccion->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta dirección'
            ], 403);
        }

        $esPredeterminada = $direccion->predeterminada;
        $direccion->delete();

        if ($esPredeterminada) {
            $nuevaPredeterminada = Direccion::where('user_id', Auth::id())->first();
            if ($nuevaPredeterminada) {
                $nuevaPredeterminada->update(['predeterminada' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada correctamente'
        ], 200);
    }
}
