<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getCart()
    {
        $cart = ShoppingCart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $cart->load('items.variantSize');

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'variant_size_id' => 'required|exists:variant_sizes,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $cart = ShoppingCart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $item = $cart->items()->where('variant_size_id', $request->variant_size_id)->first();

        if ($item) {
            $item->cantidad += $request->cantidad;
            $item->save();
        } else {
            $item = $cart->items()->create([
                'variant_size_id' => $request->variant_size_id,
                'cantidad' => $request->cantidad,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto añadido al carrito',
            'data' => $item
        ]);
    }

    public function updateItem(Request $request, CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este carrito'
            ], 403);
        }

        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        if ($request->cantidad == 0) {
            $item->delete();
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito'
            ]);
        }

        $item->cantidad = $request->cantidad;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Cantidad actualizada correctamente',
            'data' => $item
        ]);
    }

    public function removeItem(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este carrito'
            ], 403);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    public function clearCart()
    {
        $cart = ShoppingCart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'El carrito ya está vacío'
            ], 404);
        }

        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente'
        ]);
    }
}
