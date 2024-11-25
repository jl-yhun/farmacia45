<?php

namespace App\Classes\CierreCaja\Implementations;

use App\AperturaCaja;
use App\Classes\CierreCaja\Contracts\IGastosCalculator;
use App\Classes\CierreCajaGastos;
use App\Enums\FuenteGasto;
use App\Repositories\IAperturasCajaRepository;

class GastosCalculator implements IGastosCalculator
{
    private IAperturasCajaRepository $_aperturaCajaRepository;

    public function __construct(
        IAperturasCajaRepository $aperturaCajaRepository
    ) {
        $this->_aperturaCajaRepository = $aperturaCajaRepository;
    }

    public function calculate(): CierreCajaGastos
    {
        $apertura = $this->_aperturaCajaRepository->getCurrent();
        $gastos = new CierreCajaGastos();

        foreach ($apertura->gastos as $gasto) {
            if ($gasto->fuente == FuenteGasto::Caja->value)
                $gastos->gastos_efe += $gasto->monto;
            else if ($gasto->fuente == FuenteGasto::MercadoPago->value)
                $gastos->gastos_ele += $gasto->monto;
            else if ($gasto->fuente == FuenteGasto::RecargasServicios->value)
                $gastos->gastos_recargas_servicios += $gasto->monto;
        }

        foreach ($apertura->apartados as $apartado) {
            if ($this->wasGastoFromApartados($apartado)) {
                $gastos->gastos_apartados += abs($apartado->monto);
            } else {
                $gastos->apartados_dia += $apartado->monto;
            }
        }

        return $gastos;
    }

    private function wasGastoFromApartados($apartado)
    {
        return $apartado->monto < 0;
    }
}
