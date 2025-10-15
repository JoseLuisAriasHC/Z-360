<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Fallo en Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .main {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }

        .container {
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
            padding: 30px 24px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }

        .content {
            padding: 32px 24px;
            color: #4A5568;
        }

        .details-box {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 32px;
            background-color: #f6f7f8;
            color: #718096;
            margin-top: 32px;
        }

        .details-box h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
        }

        .detail-item {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .detail-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            color: #666;
            font-weight: 600;
        }

        .detail-value {
            color: #4A5568;
            margin: 0;
        }

        .total {
            color: #1a202c;
            font-weight: 600;
        }

        .error-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 32px;
        }

        .error-box h2 {
            font-size: 20px;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 12px;
        }

        .error-box p {
            color: #b91c1c;
            font-size: 16px;
            margin: 0;
        }

        .actions-section h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
        }

        .actions-section>p {
            margin-bottom: 16px;
        }

        .actions-list {
            list-style-position: inside;
            padding-left: 0;
        }

        .actions-list li {
            margin-bottom: 12px;
            padding-left: 8px;
        }

        .actions-list li strong {
            color: #1a202c;
        }

        .cta-button-container {
            text-align: center;
            margin-top: 32px;
        }

        .cta-button {
            display: inline-block;
            background-color: #3b82f6;
            text-decoration: none;
            color: white !important;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
        }

        .footer {
            background-color: #f6f7f8;
            text-align: center;
            padding: 24px;
            border-top: 1px solid #E2E8F0;
            color: #718096;
            font-size: 14px;
        }

        .footer-links {
            margin-top: 16px;
        }

        .footer-links a {
            color: #718096;
            text-decoration: none;
            margin: 0 8px;
        }

        .footer-separator {
            margin: 0 8px;
            color: #718096;
        }

    </style>
</head>

<body>
    <div class="main">
        <div class="container">
            <div class="header">
                <h1>Notificación de Fallo en Pedido</h1>
            </div>

            <div class="content">
                <p>Atención Administrador,</p>
                <p>Se ha detectado un problema con un pedido reciente. Se requiere revisión y acción. A continuación, se
                    presentan los detalles técnicos.</p>

                <div class="details-box">
                    <h2>Detalles del Pedido</h2>
                    <div class="details-grid">
                        <div class="detail-item">
                            <p class="detail-label">ID de Pedido:</p>
                            <p class="detail-value">#{{ $order->id }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-label">Fecha del Pedido:</p>
                            <p class="detail-value">{{ $order->fecha_pedido->format('d \d\e F, Y - H:i:s') }} UTC</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-label">ID de Cliente:</p>
                            <p class="detail-value">
                                {{ $order->user_id ? 'USER-' . $order->user_id : 'GUEST-' . $order->token }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-label">Email del Cliente:</p>
                            <p class="detail-value">{{ $order->envio_email }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-label">Método de Pago:</p>
                            <p class="detail-value">{{ ucfirst($order->metodo_pago) }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="detail-label">Total del Pedido:</p>
                            <p class="detail-value total">€{{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="error-box">
                    <h2>Descripción del Error</h2>
                    <p>{{ $errorMessage ?? 'Error al procesar el envío de la factura. El email no pudo ser enviado al cliente. Se requiere acción manual.' }}
                    </p>

                    @if (isset($exception))
                        <p style="margin-top: 12px;">
                            <strong>Excepción:</strong> {{ get_class($exception) }}<br>
                            <strong>Mensaje:</strong> {{ $exception->getMessage() }}
                        </p>
                    @endif
                </div>

                <div class="actions-section">
                    <h2>Acciones Sugeridas</h2>
                    <p>Se recomienda realizar las siguientes acciones de inmediato:</p>
                    <ol class="actions-list">
                        <li><strong>Verificar el pedido:</strong> Revisar el estado del pedido #{{ $order->id }} en
                            el
                            panel de administración.</li>
                        <li><strong>Contactar al cliente:</strong> Informar al cliente en {{ $order->envio_email }}
                            sobre
                            el estado de su pedido.</li>
                        <li><strong>Reenviar factura:</strong> Intentar reenviar la factura manualmente desde el panel
                            de
                            administración.</li>
                        <li><strong>Revisar logs:</strong> Consultar los logs del sistema para obtener más detalles del
                            error.</li>
                    </ol>
                </div>

                <div class="cta-button-container">
                    <a href="{{ config('app.url') }}/admin/orders/{{ $order->id }}" class="cta-button">
                        Ver Pedido en el Panel
                    </a>
                </div>
            </div>

            <div class="footer">
                <p>Correo de notificación interno © {{ date('Y') }} {{ config('app.name') }}</p>
                <div class="footer-links">
                    <a href="{{ config('app.url') }}/admin">Panel de Administración</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
