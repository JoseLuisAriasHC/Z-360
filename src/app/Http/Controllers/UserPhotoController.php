<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use App\Models\WebSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $photos = UserPhoto::where('product_id', $validated['product_id'])->get();

        return response()->json([
            'success' => true,
            'data' => $photos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'foto'       => 'required|image',
        ]);

        $maxFotos = (int) WebSettings::getValue('max_fotos_producto_por_usuario', 4);
        $count = UserPhoto::where('user_id', $validated['user_id'])
            ->where('product_id', $validated['product_id'])
            ->count();

        if ($count >= $maxFotos) {
            return response()->json([
                'success' => false,
                'message' => "No puedes subir más de $maxFotos fotos para este producto.",
            ], 422);
        }

        $path = $request->file('foto')->store('user_photos', 'public');

        $photo = UserPhoto::create([
            'user_id'    => $validated['user_id'],
            'product_id' => $validated['product_id'],
            'path'        => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto subida correctamente',
            'data'    => $photo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPhoto $userPhoto)
    {
        return response()->json([
            'success' => true,
            'data' => $userPhoto
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPhoto $userPhoto)
    {
        if ($userPhoto->path && Storage::disk('public')->exists($userPhoto->path)) {
            Storage::disk('public')->delete($userPhoto->path);
        }

        $userPhoto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada correctamente',
        ]);
    }

    /**
     * Aprobar una foto
     */
    public function aprobar(UserPhoto $userPhoto)
    {
        $userPhoto->update(['aprobado' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Foto aprobada',
            'data'    => $userPhoto,
        ]);
    }

    /**
     * Descartar una sola foto
     */
    public function descartar(UserPhoto $userPhoto)
    {
        $userPhoto->update(['aprobado' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Foto descartada correctamente',
            'data'    => $userPhoto,
        ]);
    }

    /**
     * Eliminar todas las fotos descartadas
     */
    public function eliminarNoAprobadas($product_id)
    {
        $fotos = UserPhoto::where('product_id', $product_id)
            ->where('aprobado', false)
            ->get();

        foreach ($fotos as $foto) {
            if ($foto->path && Storage::disk('public')->exists($foto->path)) {
                Storage::disk('public')->delete($foto->path);
            }
            $foto->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Fotos no aprobadas descartadas correctamente',
            'count'   => $fotos->count(),
        ]);
    }
}
