<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura - Pedido #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #3b82f6;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
        }

        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .order-info.direccion .info-row {
            margin-bottom: 0px;
        }

        .order-info h2 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 18px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            color: #666;
        }

        .info-value {
            display: table-cell;
            color: #333;
        }

        .order-info.direccion .info-value{
            width: 50%;
        }

        .products-summary {
            margin: 30px 0;
        }

        .product-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            display: table;
            width: 100%;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-info {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }

        .product-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .product-details {
            font-size: 14px;
            color: #666;
        }

        .product-pricing {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 30%;
        }

        .quantity {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .price {
            font-weight: bold;
            color: #333;
        }

        .totals-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .total-label {
            display: table-cell;
            width: 70%;
            color: #666;
        }

        .total-amount {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            color: #333;
        }

        .final-total {
            border-top: 2px solid #3b82f6;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
        }

        .final-total .total-label,
        .final-total .total-amount {
            color: #333;
            font-weight: bold;
        }

        .attachment-notice {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #1565c0;
        }

        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }

        .margin-top {
            margin-top: 10px;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0 10px;
            }

            .content {
                padding: 20px;
            }

            .info-row,
            .total-row,
            .product-item {
                display: block;
            }

            .info-label,
            .total-label {
                display: block;
                width: auto;
                margin-bottom: 5px;
            }

            .info-value,
            .total-amount {
                display: block;
                width: auto;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="header">
                <h1>¡Gracias por tu compra! 🛍️</h1>
                <p>Tu pedido #{{ $order->id }} ha sido confirmado</p>
            </div>

            <div class="content">
                <p>Hola <strong>{{ $order->envio_nombre }}</strong>,</p>
                <p>Nos complace confirmar que tu pedido ha sido procesado exitosamente.</p>

                <div class="order-info">
                    <h2>Información del Pedido</h2>
                    <div class="info-row">
                        <div class="info-label">Número de pedido:</div>
                        <div class="info-value">#{{ $order->id }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha:</div>
                        <div class="info-value">
                            {{ is_string($order->fecha_pedido) ? $order->fecha_pedido : $order->fecha_pedido->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">{{ ucfirst($order->estado) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Método de pago:</div>
                        <div class="info-value">{{ ucfirst($order->metodo_pago) }}</div>
                    </div>
                </div>

                <div class="order-info direccion">
                    <div class="info-row">
                        <div class="info-value">
                            <h2>Dirección de envío</h2>
                        </div>
                        <div class="info-value">
                            <h2>Dirección facturación</h2>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-value">{{ $order->envio_nombre }}
                        </div>
                        <div class="info-value">{{ $order->facturacion_nombre }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-value">{{ $order->envio_direccion_calle }}
                            {{ $order->envio_direccion_numero_calle }}
                        </div>
                        <div class="info-value">{{ $order->facturacion_direccion_calle }}
                            {{ $order->facturacion_direccion_numero_calle }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-value">{{ $order->envio_direccion_cp }}
                            {{ $order->envio_direccion_ciudad }}
                        </div>
                        <div class="info-value">{{ $order->facturacion_direccion_cp }}
                            {{ $order->facturacion_direccion_ciudad }}
                        </div>
                    </div>

                    <div class="margin-top">
                        <div class="info-row">
                            <div class="info-value">{{ $order->envio_email }}
                            </div>
                            <div class="info-value">{{ $order->facturacion_email }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-value">{{ $order->envio_telefono }}
                            </div>
                            <div class="info-value">{{ $order->facturacion_telefono }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="products-summary">
                    <h2>Productos pedidos</h2>
                    @foreach ($order->items as $item)
                        <div class="product-item">
                            <div class="product-info">
                                <div class="product-name">
                                    {{ $item->variantSize->productVariant->product->nombre ?? 'Producto no disponible' }}
                                </div>
                                <div class="product-details">
                                    @if ($item->variantSize && $item->variantSize->productVariant && $item->variantSize->productVariant->color)
                                        Color: {{ $item->variantSize->productVariant->color->nombre }}
                                    @endif
                                    @if ($item->variantSize && $item->variantSize->talla)
                                        | Talla: {{ $item->variantSize->talla->numero }}
                                    @endif
                                </div>
                            </div>
                            <div class="product-pricing">
                                <div class="quantity">Cantidad: {{ $item->cantidad }}</div>
                                <div class="price">{{ number_format($item->precio_unitario * $item->cantidad, 2) }} €
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="totals-box">
                    <div class="total-row">
                        <div class="total-label">Subtotal:</div>
                        <div class="total-amount">{{ number_format($order->subtotal, 2) }} €</div>
                    </div>

                    @if ($order->descuento > 0)
                        <div class="total-row">
                            <div class="total-label">Descuento:</div>
                            <div class="total-amount" style="color: #4caf50;">
                                -€{{ number_format($order->descuento, 2) }}</div>
                        </div>
                    @endif

                    @if (isset($order->costo_envio) && $order->costo_envio > 0)
                        <div class="total-row">
                            <div class="total-label">Coste de envío:</div>
                            <div class="total-amount">{{ number_format($order->costo_envio, 2) }} €</div>
                        </div>
                    @elseif(isset($order->costo_envio))
                        <div class="total-row">
                            <div class="total-label">Coste de envío:</div>
                            <div class="total-amount" style="color: #4caf50;">¡Gratis!</div>
                        </div>
                    @endif

                    <div class="total-row final-total">
                        <div class="total-label">TOTAL:</div>
                        <div class="total-amount">{{ number_format($order->total, 2) }} €</div>
                    </div>
                </div>

                <div class="attachment-notice">
                    <strong>Factura adjunta</strong><br>
                    Hemos adjuntado tu factura detallada en formato PDF a este correo para tus registros.
                </div>
            </div>

            <div class="footer">
                <p>Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos.</p>
                <p><a href="mailto:{{ $settings['empresa_email'] }}">{{ $settings['empresa_email'] }}</a> |
                    {{ $settings['empresa_telefono'] }}</p>
                <p><small>Este es un correo automático, por favor no respondas directamente.</small></p>
            </div>
        </div>
    </div>
</body>

</html>
