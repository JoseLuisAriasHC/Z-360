<?php

namespace App\Http\Controllers;

use App\Enums\EstadoOrder;
use App\Enums\EstadoPagoOrder;
use App\Enums\EstadoPagoStripe;
use App\Http\Requests\GuestOrderRequest;
use App\Http\Requests\UserOrderRequest;
use App\Jobs\SendFacturaMailJob;
use App\Models\Order;
use App\Models\ShoppingCart;
use App\Models\VariantSize;
use App\Services\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Str;

class OrderController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Listado de pedidos del usuario autenticado (paginado)
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
     * Mostrar un pedido
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
            return response()->json(['success' => false, 'message' => 'Autenticación requerida'], 401);
        }

        $payload = $request->validated();

        $cart = ShoppingCart::where('user_id', $userId)->first();
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío'], 422);
        }

        return DB::transaction(function () use ($cart, $payload, $userId) {
            $order = $this->createOrder($payload, null, $userId);
            $subtotal = $this->addItemsAndCalculateSubtotal($order, $cart->items, true);

            $order->subtotal = round($subtotal, 2);
            $order->applyCupon($order->cupon_codigo);
            $order->calculateTotal();
            $order->save();

            $paymentResult = $this->stripeService->createPaymentIntent($order);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message']);
            }

            $cart->items()->delete();

            return response()->json([
                'success' => true,
                'data' => $order->load('items'),
                'payment' => [
                    'clientSecret' => $paymentResult['clientSecret'],
                    'paymentIntentId' => $paymentResult['paymentIntentId']
                ]
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
            $order = $this->createOrder($data, $token, null);

            $subtotal = $this->addItemsAndCalculateSubtotal($order, $data['items'], false);

            $order->subtotal = round($subtotal, 2);
            $order->applyCupon($order->cupon_codigo);
            $order->calculateTotal();
            $order->save();

            $paymentResult = $this->stripeService->createPaymentIntent($order);

            if (!$paymentResult['success']) {
                throw new Exception($paymentResult['message']);
            }

            return response()->json([
                'success' => true,
                'data' => $order->load('items'),
                'token' => $token,
                'payment' => [
                    'clientSecret' => $paymentResult['clientSecret'],
                    'paymentIntentId' => $paymentResult['paymentIntentId']
                ]
            ], 201);
        });
    }

    /**
     * Confirmar pago exitoso (llamado desde frontend después de Stripe)
     */
    public function confirmarPago(Request $request, Order $order)
    {
        $request->validate([
            'payment_intent_id' => 'required|string'
        ]);

        if ($order->pago_id !== $request->payment_intent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Payment Intent no coincide con el pedido'
            ], 400);
        }

        $verification = $this->stripeService->verificarPago($request->payment_intent_id);

        if (!$verification['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el pago'
            ], 500);
        }

        if ($verification['status'] === EstadoPagoStripe::EXITOSO->value) {
            $order->update([
                'pago_estado' => EstadoPagoOrder::EXITOSO->value,
                'pago_fecha' => now(),
                'estado' => EstadoOrder::CONFIRMADO->value
            ]);

            $this->enviarFacturaPorEmail($order);

            return response()->json([
                'success' => true,
                'message' => 'Pago confirmado exitosamente',
                'data' => $order->fresh()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'El pago no ha sido completado',
            'payment_status' => $verification['status']
        ], 400);
    }

    /**
     * Webhook para recibir eventos de Stripe
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        $result = $this->stripeService->handleWebhook($payload, $signature);

        if ($result) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Crear pedido base
     */
    private function createOrder(array $data, ?string $token = null, ?int $userId = null): Order
    {
        $usarMismaDireccion = $data['usar_misma_direccion_facturacion'] ?? true;
        $orderData = [
            'user_id' => $userId,
            'token' => $token,
            'cupon_codigo' => $data['cupon_codigo'] ?? null,
            'estado' => 'pendiente',
            'subtotal' => 0,
            'descuento' => 0,
            'total' => 0,
            'envio_nombre' => $data['envio_nombre'],
            'envio_email' => $data['envio_email'],
            'envio_telefono' => $data['envio_telefono'],
            'envio_direccion_calle' => $data['envio_direccion_calle'],
            'envio_direccion_numero_calle' => $data['envio_direccion_numero_calle'],
            'envio_direccion_piso_info' => $data['envio_direccion_piso_info'] ?? null,
            'envio_direccion_ciudad' => $data['envio_direccion_ciudad'],
            'envio_direccion_cp' => $data['envio_direccion_cp'],

            'usar_misma_direccion_facturacion' => $usarMismaDireccion,
            'fecha' => Carbon::now(),
        ];

        if ($usarMismaDireccion) {
            $orderData['facturacion_nombre'] = $data['envio_nombre'];
            $orderData['facturacion_email'] = $data['envio_email'];
            $orderData['facturacion_telefono'] = $data['envio_telefono'];
            $orderData['facturacion_direccion_calle'] = $data['envio_direccion_calle'];
            $orderData['facturacion_direccion_numero_calle'] = $data['envio_direccion_numero_calle'];
            $orderData['facturacion_direccion_piso_info'] = $data['envio_direccion_piso_info'] ?? null;
            $orderData['facturacion_direccion_ciudad'] = $data['envio_direccion_ciudad'];
            $orderData['facturacion_direccion_cp'] = $data['envio_direccion_cp'];
        } else {
            $orderData['facturacion_nombre'] = $data['facturacion_nombre'];
            $orderData['facturacion_email'] = $data['facturacion_email'];
            $orderData['facturacion_telefono'] = $data['facturacion_telefono'];
            $orderData['facturacion_direccion_calle'] = $data['facturacion_direccion_calle'];
            $orderData['facturacion_direccion_numero_calle'] = $data['facturacion_direccion_numero_calle'];
            $orderData['facturacion_direccion_piso_info'] = $data['facturacion_direccion_piso_info'] ?? null;
            $orderData['facturacion_direccion_ciudad'] = $data['facturacion_direccion_ciudad'];
            $orderData['facturacion_direccion_cp'] = $data['facturacion_direccion_cp'];
        }
        return Order::create($orderData);
    }

    /**
     * Agregar items al pedido y calcular subtotal
     */
    private function addItemsAndCalculateSubtotal(Order $order, iterable $items, bool $fromCart = false): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $variantSizeId = $fromCart ? $item->variant_size_id : $item['variant_size_id'];
            $cantidad = $fromCart ? $item->cantidad : $item['cantidad'];

            $variantSize = VariantSize::with('productVariant')->findOrFail($variantSizeId);
            $precio = $variantSize->productVariant->precio;

            $order->items()->create([
                'variant_size_id' => $variantSize->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
            ]);

            $subtotal += $precio * $cantidad;
        }

        return $subtotal;
    }

    /**
     * Enviar factura por email
     */
    private function enviarFacturaPorEmail(Order $order): void
    {
        SendFacturaMailJob::dispatch($order);

        Log::info('Job de envío de factura ejecutado', [
            'order_id' => $order->id,
            'email' => $order->envio_email
        ]);
    }
}