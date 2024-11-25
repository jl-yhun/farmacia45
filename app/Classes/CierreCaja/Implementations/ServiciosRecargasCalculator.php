<?php

namespace App\Classes\CierreCaja\Implementations;

use App\AperturaCaja;
use App\Classes\CierreCaja\Contracts\IServiciosRecargasCalculator;
use App\Classes\CierreCajaServiciosRecargasComisiones;
use App\Enums\Config;
use App\Enums\MetodoPago;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IConfiguracionRepository;

class ServiciosRecargasCalculator implements IServiciosRecargasCalculator
{
    private $_config;
    private IAperturasCajaRepository $_aperturaCajaRepository;

    public function __construct(
        IAperturasCajaRepository $aperturaCajaRepository,
        IConfiguracionRepository $config
    ) {
        $this->_config = $config;
        $this->_aperturaCajaRepository = $aperturaCajaRepository;
    }

    public function calculate(): CierreCajaServiciosRecargasComisiones
    {
        $apertura = $this->_aperturaCajaRepository->getCurrent();
        $serviciosRecargasComisiones = new CierreCajaServiciosRecargasComisiones();

        foreach ($apertura->servicios as $servicio) {
            if ($servicio->metodo_pago == MetodoPago::Efectivo->value) {
                $serviciosRecargasComisiones->servicios_recargas_efe += $servicio->monto;
                $serviciosRecargasComisiones->comisiones_efe += $servicio->comision;
            } else if (
                $servicio->metodo_pago == MetodoPago::TarjetaDebito->value ||
                $servicio->metodo_pago == MetodoPago::TarjetaCredito->value
            ) {
                $serviciosRecargasComisiones->servicios_recargas_ele += $servicio->monto;
                $serviciosRecargasComisiones->comisiones_ele += $servicio->comision;
            }
        }

        foreach ($apertura->recargas as $recarga) {
            if ($recarga->metodo_pago == MetodoPago::Efectivo->value) {
                $serviciosRecargasComisiones->servicios_recargas_efe += $recarga->monto;
                $serviciosRecargasComisiones->comisiones_efe += (float) $this->_config->get(Config::ComisionRecargas);
            } else if (
                $recarga->metodo_pago == MetodoPago::TarjetaDebito->value ||
                $recarga->metodo_pago == MetodoPago::TarjetaCredito->value
            ) {
                $serviciosRecargasComisiones->servicios_recargas_ele += $recarga->monto;
                $serviciosRecargasComisiones->comisiones_ele += (float) $this->_config->get(Config::ComisionRecargas);
            }
        }

        return $serviciosRecargasComisiones;
    }
}
