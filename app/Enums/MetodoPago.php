<?php
namespace App\Enums;

enum MetodoPago: string {
    case Efectivo = 'Efectivo';
    case TarjetaDebito = 'Tarjeta de débito';
    case TarjetaCredito = 'Tarjeta de crédito';
    case Transferencia = 'Transferencia';
} 