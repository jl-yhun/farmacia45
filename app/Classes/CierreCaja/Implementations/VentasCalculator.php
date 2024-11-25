<?php

namespace App\Classes\CierreCaja\Implementations;

use App\AperturaCaja;
use App\Classes\CierreCaja\Contracts\IVentasCalculator;
use App\Classes\CierreCajaUtilidadesVentas;
use App\Enums\Config;
use App\Enums\MetodoPago;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IConfiguracionRepository;

class VentasCalculator implements IVentasCalculator
{
    private IAperturasCajaRepository $_aperturaCajaRepository;
    private IConfiguracionRepository $_config;

    public function __construct(
        IAperturasCajaRepository $aperturaCajaRepository,
        IConfiguracionRepository $config
    ) {
        $this->_aperturaCajaRepository = $aperturaCajaRepository;
        $this->_config = $config;
    }

    public function calculate(): CierreCajaUtilidadesVentas
    {
        $apertura = $this->_aperturaCajaRepository->getCurrent();
        $result = new CierreCajaUtilidadesVentas();
        $transferencias = 0;
        foreach ($apertura->ventas as $venta) {
            if ($venta->hasCancelaciones)
                continue;
            $result->utilidades += $venta->utilidad;
            if ($venta->metodo_pago == MetodoPago::Efectivo->value)
                $result->ventas_efe += $venta->total;
            else if (
                $venta->metodo_pago == MetodoPago::TarjetaDebito->value ||
                $venta->metodo_pago == MetodoPago::TarjetaCredito->value
            ) {
                $result->ventas_ele += $venta->total;
            } else {
                // Transferencias
                $transferencias += $venta->total;
            }
        }
        // Calculate comisiones Mercado Pago without including Transferencias (free of fees)
        $comisionesMp = $this->calculateCostoMercadoPago($result);
        $result->ventas_ele -= $comisionesMp;
        $result->utilidades -= $comisionesMp;

        $result->ventas_ele += $transferencias;

        $result->ventas_ele = round($result->ventas_ele, 2);
        $result->utilidades = round($result->utilidades, 2);

        return $result;
    }

    private function calculateCostoMercadoPago(CierreCajaUtilidadesVentas $utilidadesVentas)
    {
        return $utilidadesVentas->ventas_ele * floatval($this->_config->get(Config::MercadoPagoPoint));
    }
}
