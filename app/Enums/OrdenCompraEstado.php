<?php

namespace App\Enums;

enum OrdenCompraEstado: string
{
    case Pendiente = 'Pendiente';
    case Pedido = 'Pedido';
    case Recibido = 'Recibido';
    case Aplicado = 'Aplicado';
}
