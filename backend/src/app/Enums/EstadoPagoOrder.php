<?php

namespace App\Enums;

enum EstadoPagoOrder: string
{
    case PENDIENTE = 'pendiente';
    case PROCESANDO = 'procesando';
    case EXITOSO = 'exitoso';
    case FALLIDO = 'fallido';
    case REEMBOLSADO = 'reembolsado';
    case CANCELADO = 'cancelado';
}