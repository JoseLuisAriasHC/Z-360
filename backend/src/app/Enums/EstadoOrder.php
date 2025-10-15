<?php

namespace App\Enums;

enum EstadoOrder: string
{
    case PENDIENTE = 'pendiente';
    case PROCESANDO = 'procesando';
    case CONFIRMADO = 'confirmado';
    case ENVIADO = 'enviado';
    case ENTREGADO = 'entregado';
    case CANCELADO = 'cancelado';
}
