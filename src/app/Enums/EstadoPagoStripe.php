<?php

namespace App\Enums;

enum EstadoPagoStripe: string
{
    case CANCELADO = 'canceled';
    case PROCESANDO = 'processing';
    case REQUIERE_ACCION = 'requires_action';
    case REQUIERE_CAPTURA = 'requires_capture';
    case REQUIERE_CONFIRMACION = 'requires_confirmation';
    case REQUIERE_METODO_DE_PAGO = 'requires_payment_method';
    case EXITOSO = 'succeeded';
}