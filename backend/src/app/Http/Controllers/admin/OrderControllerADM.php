<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderControllerADM extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['items.variantSize', 'user']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Actualizar estado de un pedido (admin).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,procesando,enviado,entregado,cancelado'
        ]);

        $order->estado = $request->input('estado');
        $order->save();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
