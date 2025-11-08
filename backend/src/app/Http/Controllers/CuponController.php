<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    public function getByCodigo($codigo)
    {
        $cupon = Cupon::where('codigo', $codigo)
            ->where(function ($query) {
                $query->whereNull('fecha_expiracion')
                    ->orWhere('fecha_expiracion', '>=', now());
            })
            ->where('uso_maximo', '>', 0)
            ->first();

        if (!$cupon) {
            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos, pero el código que has introducido no es correcto o no es válido.Vuelve a intentarlo o prueba con otro código.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $cupon,
        ]);
    }
}
