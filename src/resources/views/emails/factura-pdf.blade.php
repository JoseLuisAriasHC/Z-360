<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Factura - Pedido #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            line-height: 1.4;
            color: #374151;
            background-color: #f3f4f6;
            padding: 40px 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .invoice-content {
            padding: 40px;
        }

        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            width: 40px;
            height: 40px;
            background-color: #3b82f6;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 14px;
            color: #6b7280;
        }

        .company-info {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.5;
        }

        .company-name {
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .billing-info {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }

        .billing-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .billing-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .customer-info {
            font-size: 12px;
            line-height: 1.3;
            color: #374151;
        }

        .customer-name {
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .invoice-details {
            font-size: 12px;
            line-height: 1.8;
            color: #374151;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: none;
        }

        .products-table thead {
            background-color: #f9fafb;
            text-align: center
        }

        .products-table th {
            padding: 13px 10px;
            font-weight: bold;
            font-size: 10px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .products-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            text-align: center;
        }

        .products-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-image {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f3f4f6;
        }

        td.product-td {
            text-align: left;
            padding-left: 40px;
        }

        .product-name {
            font-size: 12px;
            color: #111827;
            font-weight: 500;
        }

        .product-details {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .number-cell {
            font-size: 12px;
            color: #374151;
        }

        .total-cell {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
        }

        .totals-section {
            margin-top: 30px;
            padding-top: 20px;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            font-size: 12px;
            padding: 4px 0px;
        }

        .totals-table .label {
            text-align: left;
            color: #6b7280;
            padding-right: 40px;
        }

        .totals-table .amount {
            text-align: right;
            color: #374151;
            font-family: monospace;
        }

        .total-final {
            border-top: 2px solid #e5e7eb;
            padding-top: 15px !important;
            margin-top: 10px;
        }

        .total-final .label {
            font-weight: bold;
            font-size: 12px;
            color: #111827;
        }

        .total-final .amount {
            font-weight: bold;
            font-size: 14px;
            color: #111827;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }

        .footer-link {
            color: #3b82f6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-content">
            <!-- Header -->
            <div class="invoice-header">
                <div class="header-left">
                    <div class="company-logo"></div>
                    <div class="invoice-title">Factura</div>
                    <div class="invoice-number">#Nº-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="header-right">
                    <div class="company-name">{{ $settings['empresa_nombre'] }}</div>
                    <div class="company-info">
                        {{ $settings['empresa_direccion'] }}<br>
                        {{ $settings['empresa_email'] }}<br>
                        {{ $settings['empresa_telefono'] }}
                    </div>
                </div>
            </div>

            <!-- Billing Information -->
            <div class="billing-info">
                <div class="billing-left">
                    <div class="section-title">Envio A</div>
                    <div class="customer-info">
                        <div class="customer-name">{{ $order->envio_nombre }}</div>
                        {{ $order->envio_direccion_calle }} {{ $order->envio_direccion_numero_calle }}, 
                        @if ($order->envio_direccion_piso_info)
                            {{ $order->envio_direccion_piso_info }}<br>
                        @endif
                        {{ $order->envio_direccion_ciudad }}, {{ $order->envio_direccion_cp }}<br>
                        {{ $order->envio_email }}<br>
                        {{ $order->envio_telefono }}
                    </div>
                </div>
                <div class="billing-right">
                    <div class="section-title">Factura A</div>
                    <div class="customer-info">
                        @if ($order->usar_misma_direccion_facturacion)
                            <div class="customer-name">{{ $order->envio_nombre }}</div>
                            {{ $order->envio_direccion_calle }} {{ $order->envio_direccion_numero_calle }}, 
                            @if ($order->envio_direccion_piso_info)
                                {{ $order->envio_direccion_piso_info }}<br>
                            @endif
                            {{ $order->envio_direccion_ciudad }}, {{ $order->envio_direccion_cp }}<br>
                            {{ $order->facturacion_email }}<br>
                            {{ $order->envio_telefono }}
                        @else
                            <div class="customer-name">{{ $order->facturacion_nombre }}</div>
                            {{ $order->facturacion_direccion_calle }} {{ $order->facturacion_direccion_numero_calle }}, 
                            @if ($order->facturacion_direccion_piso_info)
                                {{ $order->facturacion_direccion_piso_info }}<br>
                            @endif
                            {{ $order->facturacion_direccion_ciudad }}, {{ $order->facturacion_direccion_cp }}<br>
                            {{ $order->facturacion_email }}<br>
                            {{ $order->facturacion_telefono }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 80px;"></th>
                        <th style="width: 40%;">Producto</th>
                        <th style="width: 15%;">Cantidad</th>
                        <th style="width: 20%;">Precio Unitario (con IVA)</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>
                                @if ($item->imagen_base64)
                                    <img class="product-image" src="{{ $item->imagen_base64 }}"
                                        alt="{{ $item->variantSize->productVariant->product->nombre ?? 'Producto' }}">
                                @else
                                    <img class="product-image" src="#"
                                        alt="{{ $item->variantSize->productVariant->product->nombre ?? 'Producto' }}">
                                @endif
                            </td>
                            <td class="product-td">
                                <div class="product-name">
                                    {{ $item->variantSize->productVariant->product->nombre ?? 'Producto no disponible' }}
                                </div>
                                @if ($item->variantSize && $item->variantSize->productVariant && $item->variantSize->productVariant->color)
                                    <div class="product-details">
                                        Color: {{ $item->variantSize->productVariant->color->nombre }}
                                        @if ($item->variantSize && $item->variantSize->talla)
                                            | Talla: {{ $item->variantSize->talla->numero }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="number-cell">{{ $item->cantidad }}</td>
                            <td class="number-cell">€{{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="total-cell">€{{ number_format($item->precio_unitario * $item->cantidad, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
                                No hay productos en esta factura
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="amount">€{{ number_format($order->subtotal_sin_iva, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">IVA ({{ (float) $settings['iva'] }}%)</td>
                        <td class="amount">€{{ number_format($order->iva, 2) }}</td>
                    </tr>
                    @if ($order->descuento > 0)
                        <tr>
                            <td class="label">Descuento</td>
                            <td class="amount" style="color: #059669;">-€{{ number_format($order->descuento, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-final">
                        <td class="label">Total</td>
                        <td class="amount">
                            €{{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="footer">
                Gracias por su compra. Si tiene alguna pregunta, contáctenos.<br>
                <a href="mailto:{{ $settings['empresa_email'] }}"
                    class="footer-link">{{ $settings['empresa_email'] }}</a>
            </div>
        </div>
    </div>
</body>

</html>
