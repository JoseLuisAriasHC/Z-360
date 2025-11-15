<?php

namespace App\Jobs;

use App\Mail\FacturaMail;
use App\Mail\FailedFacturaMail;
use App\Models\Order;
use App\Models\WebSettings;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFacturaMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos
     */
    public $tries = 3;

    /**
     * Timeout en segundos
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->order->envio_email)->send(new FacturaMail($this->order));

            Log::info('Factura enviada correctamente', [
                'order_id' => $this->order->id,
                'email' => $this->order->envio_email
            ]);
        } catch (Exception $e) {
            Log::error('Error enviando factura por email: ' . $e->getMessage(), [
                'order_id' => $this->order->id,
                'email' => $this->order->envio_email,
                'error' => $e->getMessage()
            ]);

            // Re-lanzar para que el job se reintente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de envío de factura falló definitivamente', [
            'order_id' => $this->order->id,
            'email' => $this->order->envio_email,
            'error' => $exception->getMessage()
        ]);

        // Notificar al administrador sobre el fallo
        try {
            $adminEmail = WebSettings::getValue('email_admin');
            Mail::to($adminEmail)
                ->send(new FailedFacturaMail(
                    $this->order,
                    $exception,
                    'Error al enviar factura después de ' . $this->tries . ' intentos.'
                ));

            Log::info('Notificación de fallo en el envio de la facutara al correo del cliente enviada al administrador', [
                'order_id' => $this->order->id,
                'admin_email' => $adminEmail
            ]);
        } catch (Exception $e) {
            Log::critical('No se pudo notificar al administrador sobre el fallo', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
