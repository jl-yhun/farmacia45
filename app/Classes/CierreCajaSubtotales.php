<?php

namespace App\Classes;

class CierreCajaSubtotales
{
    public $subtotal_efe;
    public $subtotal_ele;
    public $subtotal_recargas_servicios;
    public $subtotal_apartados;

    public function getTotales()
    {
        return round($this->subtotal_efe + $this->subtotal_ele, 2);
    }
}
