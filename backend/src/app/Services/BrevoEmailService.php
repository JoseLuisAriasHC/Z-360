<?php

namespace App\Services;

use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailAttachment;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class BrevoEmailService
{
    protected TransactionalEmailsApi $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            config('services.brevo.api_key')
        );

        $this->apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );
    }

    /**
     * Enviar email con adjuntos
     *
     * @param array $to ['email' => 'user@example.com', 'name' => 'Name']
     * @param string $subject
     * @param string $htmlContent
     * @param array|null $from ['email' => 'from@example.com', 'name' => 'Name']
     * @param array $attachments [['content' => base64_content, 'name' => 'file.pdf']]
     * @return mixed
     */
    public function sendEmail(
        array $to,
        string $subject,
        string $htmlContent,
        ?array $from = null,
        array $attachments = []
    ) {
        $sender = [
            'email' => $from['email'] ?? config('mail.from.address'),
            'name' => $from['name'] ?? config('mail.from.name')
        ];

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => $subject,
            'sender' => $sender,
            'to' => [
                [
                    'email' => $to['email'],
                    'name' => $to['name'] ?? ''
                ]
            ],
            'htmlContent' => $htmlContent,
        ]);

        // Agregar adjuntos si existen
        if (!empty($attachments)) {
            $brevoAttachments = [];
            foreach ($attachments as $attachment) {
                $brevoAttachments[] = new SendSmtpEmailAttachment([
                    'content' => $attachment['content'], // base64
                    'name' => $attachment['name']
                ]);
            }
            $sendSmtpEmail->setAttachment($brevoAttachments);
        }

        try {
            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            
            Log::info('Email enviado via Brevo API', [
                'to' => $to['email'],
                'subject' => $subject,
                'message_id' => $result->getMessageId()
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('Error enviando email via Brevo API', [
                'to' => $to['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}