<?php

use App\AperturaCaja;
use App\Configuracion;

if (!function_exists('_c')) {
    function _c($clave)
    {
        $c = Configuracion::where("clave", $clave)->first();
        if (!$c)
            return null;
        return $c->valor;
    }
}
if (!function_exists('getAperturaCajaIfExist')) {
    function getAperturaCajaIfExist()
    {
        return AperturaCaja::where("estado", "Pendiente")->first()->id ?? 0;
    }
}
