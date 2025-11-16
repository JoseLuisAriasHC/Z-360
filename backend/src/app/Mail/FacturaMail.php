<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\WebSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FacturaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * La orden para la cual generar la factura
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Asunto y configuración del email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura de tu pedido #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        $webSettings = $this->getWebSettings();

        return new Content(
            view: 'emails.factura',
            with: [
                'order' => $this->order,
                'items' => $this->order->items->load([
                    'variantSize.productVariant.product',
                    'variantSize.productVariant.color',
                    'variantSize.talla'
                ]),
                'settings' => $webSettings
            ]
        );
    }

    /**
     * Get the attachments for the message (PDF de la factura).
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Obtener las configuraciones web necesarias
     */
    private function getWebSettings(): array
    {
        return [
            'iva' => (float) WebSettings::getValue('iva', 21),
            'empresa_nombre' => WebSettings::getValue('empresa_nombre', 'Z-360'),
            'empresa_direccion' => WebSettings::getValue('empresa_direccion', 'Casco Antiguo, 50004 Zaragoza'),
            'empresa_telefono' => WebSettings::getValue('empresa_telefono', '+34 666 777 888 999'),
            'empresa_email' => WebSettings::getValue('empresa_email', 'z360@gmail.com'),
        ];
    }
}
