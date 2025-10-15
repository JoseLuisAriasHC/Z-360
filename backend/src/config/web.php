<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paginación por página en las tablas de la administración de la web
    |--------------------------------------------------------------------------
    |
    | Esta opción setea el numero de filas que se muestra en una página de
    | una tabla en listados de la adminitración
    |
    */

    'paginacion_por_pagina' => env('PAGINACION_POR_PAGINA', 10),

    /*
    |--------------------------------------------------------------------------
    | Paginación por página en la lista de pedidos de un usuario
    |--------------------------------------------------------------------------
    |
    | Esta opción setea el numero de pedidos que se muestran en la lista de
    | pedidos de un usuario
    |
    */

    'paginacion_por_pagina_en_pedidos' => env('PAGINACION_POR_PAGINA_EN_PEDIDOS', 10),

    /*
    |--------------------------------------------------------------------------
    | Email del adminitrador por defecto para que le lleguen corres de fallos
    |--------------------------------------------------------------------------
    |
    | Esta opción setea el correo del adminitrador por defecto para que le lleguen notificaciones
    | en caso de errores en la aplicacion como al envio de un correo en el pedido
    |
    */

    'email_admin_default' => env('EMAIL_ADMIN_DEFAULT', 10),
];
