<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\Talla;
use App\Models\UserBrandSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBrandSizeController extends Controller
{
    /**
     * Listar todas las tallas personalizadas de un usuario.
     */
    public function index(int $userId)
    {
        $tallas = UserBrandSize::with(['marca'])->where('user_id', $userId)->get();
        return response()->json([
            'success' => true,
            'data' => $tallas
        ]);
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tallas' => 'required|array',
            'tallas.*.marca_id' => 'required|exists:marcas,id',
            'tallas.*.talla_id' => 'required|exists:tallas,id',
            'borrar_tallas_personalizadas' => 'sometimes|array',
            'borrar_tallas_personalizadas.*' => 'integer|exists:user_brand_sizes,id',
        ]);

        $userId = $validated['user_id'];
        $results = [];

        DB::transaction(function () use ($validated, $userId, &$results) {
            $marcas = Marca::whereIn('id', collect($validated['tallas'])->pluck('marca_id'))->get()->keyBy('id');
            $tallas = Talla::whereIn('id', collect($validated['tallas'])->pluck('talla_id'))->get()->keyBy('id');

            foreach ($validated['tallas'] as $talla) {
                $marca = $marcas[$talla['marca_id']];
                $tallaModel = $tallas[$talla['talla_id']];
                $talla_personalizada = $tallaModel->numero + $marca->talla_offset;

                $record = UserBrandSize::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'marca_id' => $talla['marca_id'],
                    ],
                    [
                        'talla_id' => $talla['talla_id'],
                        'talla_personalizada' => $talla_personalizada
                    ]
                );

                $results[] = $record;
            }

            if (!empty($validated['borrar_tallas_personalizadas'])) {
                UserBrandSize::whereIn('id', $validated['borrar_tallas_personalizadas'])
                    ->where('user_id', $userId)
                    ->delete();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Tallas personalizadas actualizadas/guardadas correctamente',
            'data' => $results
        ]);
    }
}
