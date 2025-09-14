<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestOrderRequest;
use App\Http\Requests\UserOrderRequest;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\VariantSize;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->paginate(config('web.paginacion_por_pagina_en_pedidos', 10));

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
        if ($order->user_id && $order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $order->load('items.variantSize');

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Mostrar el último pedido de un usuario autenticado
     */
    public function showLastOrder()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para ver tus pedidos'
            ], 401);
        }

        $order = Order::where('user_id', $userId)
            ->with(['items.variantSize.productVariant'])
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes pedidos registrados'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Mostrar pedido por token publico (invitado)
     */
    public function showByToken($token)
    {
        $order = Order::where('token', $token)->with('items.variantSize')->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Crear pedido desde carrito del usuario
     */
    public function storeFromCart(UserOrderRequest $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Autenticación requerida'
            ], 401);
        }

        $payload = $request->validated();

        $cart = ShoppingCart::where('user_id', $userId)->first();
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío'], 422);
        }

        return DB::transaction(function () use ($cart, $payload, $userId) {
            $subtotal = 0;

            // Crear pedido
            $order = Order::create([
                'user_id' => $userId,
                'token' => null,
                'cupon_codigo' => $payload['cupon_codigo'] ?? null,
                'estado' => 'pendiente',
                'subtotal' => 0,
                'descuento' => 0,
                'costo_envio' => $payload['costo_envio'] ?? 0,
                'total' => 0,
                'nombre_cliente' => $payload['nombre_cliente'] ?? null,
                'email_cliente' => $payload['email_cliente'] ?? null,
                'telefono_cliente' => $payload['telefono_cliente'] ?? null,
                'direccion_id' => $payload['direccion_id'] ?? null,
                'direccion_calle' => $payload['direccion_calle'] ?? null,
                'direccion_numero_calle' => $payload['direccion_numero_calle'] ?? null,
                'direccion_piso_info' => $payload['direccion_piso_info'] ?? null,
                'direccion_ciudad' => $payload['direccion_ciudad'] ?? null,
                'direccion_cp' => $payload['direccion_cp'] ?? null,
                'metodo_pago' => $payload['metodo_pago'] ?? null,
                'fecha' => Carbon::now(),
            ]);

            // Añadir los productos al pedido desde el carrito
            foreach ($cart->items as $cartItem) {
                $variant_size = VariantSize::with('productVariant')->findOrFail($cartItem->variant_size_id);
                $precio = $variant_size->productVariant->precio;
                $cantidad = $cartItem->cantidad;

                $order->items()->create([
                    'variant_size_id' => $variant_size->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                ]);

                $subtotal += ($precio * $cantidad);
            }

            // Calcular subtotal, cupon y total
            $order->subtotal = round($subtotal, 2);
            $order->applyCupon($order->cupon_codigo);
            $order->calculateTotal();
            $order->save();

            // vaciar carrito
            $cart->items()->delete();

            return response()->json([
                'success' => true,
                'data' => $order->load('items')
            ], 201);
        });
    }

    /**
     * Crear pedido desde payload (invitado)
     */
    public function storeGuest(GuestOrderRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $token = Str::uuid()->toString();

            $order = Order::create([
                'user_id' => null,
                'token' => $token,
                'cupon_codigo' => $data['cupon_codigo'] ?? null,
                'estado' => 'pendiente',
                'subtotal' => 0,
                'descuento' => 0,
                'costo_envio' => $data['costo_envio'] ?? 0,
                'total' => 0,
                'nombre_cliente' => $data['nombre_cliente'] ?? null,
                'email_cliente' => $data['email_cliente'] ?? null,
                'telefono_cliente' => $data['telefono_cliente'] ?? null,
                'direccion_id' => $data['direccion_id'] ?? null,
                'direccion_calle' => $data['direccion_calle'] ?? null,
                'direccion_numero_calle' => $data['direccion_numero_calle'] ?? null,
                'direccion_piso_info' => $data['direccion_piso_info'] ?? null,
                'direccion_ciudad' => $data['direccion_ciudad'] ?? null,
                'direccion_cp' => $data['direccion_cp'] ?? null,
                'metodo_pago' => $data['metodo_pago'] ?? null,
                'fecha' => Carbon::now(),
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $variant_size = VariantSize::with('productVariant')->findOrFail($item['variant_size_id']);
                $precio = $variant_size->productVariant->precio;
                $cantidad = $item['cantidad'];

                $order->items()->create([
                    'variant_size_id' => $variant_size->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                ]);

                $subtotal += ($precio * $cantidad);
            }

            $order->subtotal = round($subtotal, 2);
            $order->applyCupon($order->cupon_codigo);
            $order->calculateTotal();
            $order->save();

            // TODo: enviar email, generar factura, etc

            return response()->json([
                'success' => true,
                'data' => $order->load('items'),
                'token' => $token
            ], 201);
        });
    }
}
