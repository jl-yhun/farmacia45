<?php
namespace App\Enums;

enum TipoResumenInventario: string {
    case Faltante = 'faltante';
    case Sobrante = 'sobrante';
    case Concidencia = 'concidencia';
    case Inexistencia = 'inexistencia';
} 