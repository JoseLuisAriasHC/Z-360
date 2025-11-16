<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\WebSettings;
use App\Services\BrevoEmailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFacturaMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60; // Reducido a 60 segundos
    public $backoff = 30; // Esperar 30 segundos entre reintentos

    public function __construct(
        public Order $order
    ) {}

    public function handle(BrevoEmailService $brevoService): void
    {
        try {
            // Preparar datos
            $webSettings = $this->getWebSettings();
            $items = $this->prepareItems();

            // Generar HTML del email
            $htmlContent = view('emails.factura', [
                'order' => $this->order,
                'items' => $items,
                'settings' => $webSettings
            ])->render();

            // Generar PDF
            $pdf = Pdf::loadView('emails.factura-pdf', [
                'order' => $this->order,
                'items' => $items,
                'settings' => $webSettings
            ]);

            $pdfContent = base64_encode($pdf->output());

            // Enviar via API de Brevo
            $brevoService->sendEmail(
                [
                    'email' => $this->order->envio_email,
                    'name' => $this->order->envio_nombre . ' ' . $this->order->envio_apellidos
                ],
                'Factura de tu pedido #' . $this->order->id,
                $htmlContent,
                null,
                [
                    [
                        'content' => $pdfContent,
                        'name' => 'factura-' . $this->order->id . '.pdf'
                    ]
                ]
            );

            Log::info('Factura enviada correctamente via Brevo API', [
                'order_id' => $this->order->id,
                'email' => $this->order->envio_email
            ]);

        } catch (Exception $e) {
            Log::error('Error enviando factura: ' . $e->getMessage(), [
                'order_id' => $this->order->id,
                'email' => $this->order->envio_email,
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job de factura falló definitivamente', [
            'order_id' => $this->order->id,
            'email' => $this->order->envio_email,
            'error' => $exception->getMessage()
        ]);

        // Notificar al administrador
        try {
            $adminEmail = WebSettings::getValue('email_admin');
            $brevoService = app(BrevoEmailService::class);

            $htmlContent = view('emails.failed-order-notification', [
                'order' => $this->order,
                'exception' => $exception,
                'errorMessage' => 'Error después de ' . $this->tries . ' intentos: ' . $exception->getMessage()
            ])->render();

            $brevoService->sendEmail(
                ['email' => $adminEmail, 'name' => 'Administrador'],
                '⚠️ ERROR: Fallo en envío de factura - Pedido #' . $this->order->id,
                $htmlContent
            );

        } catch (Exception $e) {
            Log::critical('No se pudo notificar al admin', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function prepareItems()
    {
        $items = $this->order->items->load([
            'variantSize.productVariant.product',
            'variantSize.productVariant.color',
            'variantSize.talla'
        ]);

        // Convertir imágenes a base64 para PDF
        foreach ($items as $item) {
            if (
                $item->variantSize &&
                $item->variantSize->productVariant &&
                $item->variantSize->productVariant->imagen_principal_jpeg
            ) {
                $jpegFilename = $item->variantSize->productVariant->imagen_principal_jpeg;
                $jpegPath = storage_path("app/public/product_variants/S_{$jpegFilename}");

                try {
                    if (file_exists($jpegPath)) {
                        $imageData = file_get_contents($jpegPath);
                        $mimeType = mime_content_type($jpegPath);
                        $item->imagen_base64 = "data:{$mimeType};base64," . base64_encode($imageData);
                    }
                } catch (Exception $e) {
                    Log::warning('Error cargando imagen para PDF', [
                        'path' => $jpegPath,
                        'error' => $e->getMessage()
                    ]);
                    $item->imagen_base64 = null;
                }
            }
        }

        return $items;
    }

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