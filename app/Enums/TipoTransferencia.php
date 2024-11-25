<?php
namespace App\Enums;

enum TipoTransferencia: string {
    case EfeEle = 'Efectivo a Electronico';
    case EleEfe = 'Electronico a Efectivo';
} 