<?php
namespace App\Enums;

enum Config: string {
    case EstadoCaja = 'ESTADO_CAJA';
    case MercadoPagoServicios = 'MERCADO_PAGO_COMISION_SERVICIOS';
    case MercadoPagoPoint = 'MERCADO_PAGO_COMISION_COBROS';
    case ComisionServicios = 'COMISION_SERVICIOS';
    case ComisionRecargas = 'COMISION_RECARGAS';
} 