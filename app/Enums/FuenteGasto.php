<?php

namespace App\Enums;

enum FuenteGasto: string
{
    case Caja = 'Caja';
    case Apartados = 'Apartados';
    case MercadoPago = 'Mercado Pago';
    case RecargasServicios = 'Recargas Servicios';
}
