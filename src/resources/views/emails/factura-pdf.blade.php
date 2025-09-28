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
            margin-bottom: 40px;
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

        .company-logo::after {
            content: "🏪";
            color: white;
            font-size: 20px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 14px;
            color: #6b7280;
        }

        .company-info {
            font-size: 14px;
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
            font-size: 14px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .customer-info {
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
        }

        .customer-name {
            font-weight: bold;
            color: #111827;
            margin-bottom: 5px;
        }

        .invoice-details {
            font-size: 14px;
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
        }

        .products-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .products-table td {
            padding: 20px 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .products-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #f3f4f6;
        }

        .product-placeholder {
            width: 60px;
            height: 60px;
            background-color: #e5e7eb;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .product-name {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
        }

        .product-details {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .quantity-cell {
            text-align: center;
            font-size: 14px;
            color: #374151;
        }

        .price-cell {
            text-align: right;
            font-size: 14px;
            color: #374151;
        }

        .total-cell {
            text-align: right;
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
            padding: 8px 0;
            font-size: 14px;
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
            font-size: 16px;
            color: #111827;
        }

        .total-final .amount {
            font-weight: bold;
            font-size: 18px;
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
                    <div class="section-title">Facturado A</div>
                    <div class="customer-info">
                        <div class="customer-name">{{ $order->nombre_cliente }}</div>
                        {{ $order->direccion_calle }} {{ $order->direccion_numero_calle }},<br>
                        @if ($order->direccion_piso_info)
                            {{ $order->direccion_piso_info }}<br>
                        @endif
                        {{ $order->direccion_ciudad }}, {{ $order->direccion_cp }}<br>
                        {{ $order->email_cliente }}<br>
                        {{ $order->telefono_cliente }}
                    </div>
                </div>
                <div class="billing-right">
                    <div class="section-title">Detalles de la Factura</div>
                    <div class="invoice-details">
                        <strong>Fecha de emisión:</strong>
                        {{ is_string($order->fecha) ? $order->fecha : $order->fecha->format('d \d\e F, Y') }}<br>
                        <strong>Fecha de vencimiento:</strong>
                        {{ is_string($order->fecha) ? $order->fecha : $order->fecha->addDays(30)->format('d \d\e F, Y') }}
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 80px;"></th>
                        <th style="width: 40%;">Producto</th>
                        <th style="width: 15%;" class="quantity-cell">Cantidad</th>
                        <th style="width: 20%;" class="price-cell">Precio Unitario (con IVA)</th>
                        <th style="width: 20%;" class="total-cell">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>
                                @if ($item->variantSize && $item->variantSize->productVariant && $item->variantSize->productVariant->imagen_principal)
                                    <img class="product-image"
                                        src="{{ asset("storage/product_variant/S_{$item->variantSize->productVariant->imagen_principal}") }}"
                                        alt="{{ $item->variantSize->productVariant->product->nombre ?? 'Producto' }}">
                                @else
                                    <div class="product-placeholder">📦</div>
                                @endif
                            </td>
                            <td>
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
                            <td class="quantity-cell">{{ $item->cantidad }}</td>
                            <td class="price-cell">€{{ number_format($item->precio_unitario, 2) }}</td>
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
