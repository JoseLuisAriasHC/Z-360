<?php

namespace App\Services;

use App\Enums\EstadoOrder;
use App\Enums\EstadoPagoOrder;
use App\Models\Order;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentMethod;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Crear un Payment Intent para un pedido
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($order->total),
                'currency' => 'eur',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_token' => $order->token ?? '',
                    'order_user_id' => $order->user_id ?? '',
                ],
                'description' => "Pedido #{$order->id} - {$order->envio_nombre}",
                'receipt_email' => $order->envio_email,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $order->update([
                'pago_id' => $paymentIntent->id,
                'pago_estado' => EstadoPagoOrder::PROCESANDO,
                'estado' => EstadoOrder::PROCESANDO
            ]);

            return [
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ];
        } catch (Exception $e) {
            Log::error('Error creando Payment Intent', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar el estado de un Payment Intent
     */
    public function verificarPago(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Convertir cantidad a formato Stripe (céntimos)
     */
    private function convertToStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Procesar webhook de Stripe
     */
    public function handleWebhook(string $payload, string $signature): bool
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            Log::info('Webhook recibido', [
                'type' => $event->type,
                'id' => $event->id
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePagoExitoso($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePagoFallido($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleReembolso($event->data->object);
                    break;
            }

            return true;
        } catch (Exception $e) {
            Log::error('Error procesando webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            return false;
        }
    }

    private function handlePagoExitoso($paymentIntent): void
    {
        $order = Order::where('pago_id', $paymentIntent->id)->first();

        if (!$order) {
            Log::warning('Pedido no encontrado para Payment Intent', [
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }

        if ($order) {
            $order->update([
                'pago_estado' => EstadoPagoOrder::EXITOSO,
                'pago_fecha' => now(),
                'estado' => EstadoOrder::CONFIRMADO,
                'metodo_pago' => $this->getMetodoPago($paymentIntent)
            ]);

            Log::info('Pago exitoso', ['order_id' => $order->id]);
        }
    }

    private function handlePagoFallido($paymentIntent): void
    {
        $order = Order::where('pago_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'pago_estado' => EstadoPagoOrder::FALLIDO,
                'estado' => 'cancelado'
            ]);

            Log::warning('Pago fallido', ['order_id' => $order->id]);
        }
    }

    private function handleReembolso($charge): void
    {
        // Buscar pedido por payment_intent_id
        $order = Order::where('pago_id', $charge->payment_intent)->first();

        if (!$order) {
            Log::warning('Pedido no encontrado para reembolso', [
                'payment_intent_id' => $charge->payment_intent
            ]);
            return;
        }

        $order->update([
            'pago_estado' => EstadoPagoOrder::REEMBOLSADO,
            'estado' => EstadoOrder::CANCELADO,
            'fecha_reembolso' => now(),
        ]);

        Log::info('Reembolso procesado', [
            'order_id' => $order->id,
            'amount' => $charge->amount / 100
        ]);
    }

    /**
     * Extraer método de pago del Payment Intent
     * 
     * Retorna: 'tarjeta', 'paypal', 'wallets', 'otro'
     */
    private function getMetodoPago($paymentIntent): string
    {
        if (!$paymentIntent->payment_method)
            return 'desconocido';

        $paymentMethod = PaymentMethod::retrieve($paymentIntent->payment_method);

        return match ($paymentMethod->type) {
            'card' => $paymentMethod->card->brand . ' ***' . $paymentMethod->card->last4,
            'paypal' => 'PayPal',
            'apple_pay' => 'Apple pay',
            'google_pay' => 'Google pay',
            'alipay' => 'Alipay',
            'sepa_debit' => 'sepa_debit',
            default => 'otro: ' . $paymentMethod->type,
        };
    }
}
