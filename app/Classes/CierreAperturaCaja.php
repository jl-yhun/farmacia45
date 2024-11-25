<?php

namespace App\Classes;

use App\AperturaCaja;
use App\Classes\CierreCaja\Contracts\IGastosCalculator;
use App\Classes\CierreCaja\Contracts\IServiciosRecargasCalculator;
use App\Classes\CierreCaja\Contracts\ITransferenciasCalculator;
use App\Classes\CierreCaja\Contracts\IVentasCalculator;

use App\Repositories\IAperturasCajaRepository;

class CierreAperturaCaja implements ICierreAperturaCajaBuilder
{
    private $apertura;
    private $_aperturaCajaRepository;
    private IVentasCalculator $_ventasCalculator;
    private IGastosCalculator $_gastosCalculator;
    private IServiciosRecargasCalculator $_serviciosRecargasCalculator;
    private ITransferenciasCalculator $_transferenciasCalculator;

    public function __construct(
        IAperturasCajaRepository $aperturaCajaRepository,
        IVentasCalculator $ventasCalculator,
        IGastosCalculator $gastosCalculator,
        IServiciosRecargasCalculator $serviciosRecargasCalculator,
        ITransferenciasCalculator $transferenciasCalculator
    ) {
        $this->_aperturaCajaRepository = $aperturaCajaRepository;
        $this->_ventasCalculator = $ventasCalculator;
        $this->_gastosCalculator = $gastosCalculator;
        $this->_serviciosRecargasCalculator = $serviciosRecargasCalculator;
        $this->_transferenciasCalculator = $transferenciasCalculator;
    }

    public function calculateEverything(): array
    {
        $this->apertura = $this->_aperturaCajaRepository->getCurrent();
        $utilidadesVentas = $this->_ventasCalculator->calculate();
        $gastos = $this->_gastosCalculator->calculate();
        $serviciosRecargasComisiones = $this->_serviciosRecargasCalculator->calculate();
        $transferencias = $this->_transferenciasCalculator->calculate();

        $subtotals = $this->calculateSubtotals(
            $this->apertura,
            $utilidadesVentas,
            $gastos,
            $serviciosRecargasComisiones,
            $transferencias
        );

        return [
            'ventas_efe' => $utilidadesVentas->ventas_efe,
            'ventas_ele' => $utilidadesVentas->ventas_ele,
            'gastos_efe' => $gastos->gastos_efe,
            'gastos_ele' => $gastos->gastos_ele,
            'apartados_dia' => $gastos->apartados_dia,
            'gastos_apartados' => $gastos->gastos_apartados,
            'gastos_recargas_servicios' => $gastos->gastos_recargas_servicios,
            'servicios_recargas_efe' => $serviciosRecargasComisiones->servicios_recargas_efe + $serviciosRecargasComisiones->comisiones_efe,
            'subtotal_efe' => $subtotals->subtotal_efe,
            'subtotal_ele' => $subtotals->subtotal_ele,
            'subtotal_apartados' => $subtotals->subtotal_apartados,
            'subtotal_recargas_servicios' => $subtotals->subtotal_recargas_servicios,
            'total' => $subtotals->getTotales(),
            'utilidades' => $utilidadesVentas->utilidades + $serviciosRecargasComisiones->comisiones_efe + $serviciosRecargasComisiones->comisiones_ele
        ];
    }

    private function calculateSubtotals(
        AperturaCaja $apertura,
        CierreCajaUtilidadesVentas $utilidades,
        CierreCajaGastos $gastos,
        CierreCajaServiciosRecargasComisiones $servicios,
        CierreCajaTransferencias $transferencias
    ): CierreCajaSubtotales {
        $totales = new CierreCajaSubtotales();

        $totales->subtotal_efe = $apertura->inicial_efe +
            $utilidades->ventas_efe -
            $gastos->gastos_efe + $transferencias->ele_efe - $transferencias->efe_ele -
            $gastos->apartados_dia;

        $totales->subtotal_ele = $apertura->inicial_ele +
            $utilidades->ventas_ele - $gastos->gastos_ele +
            $transferencias->efe_ele - $transferencias->ele_efe +
            $servicios->servicios_recargas_ele + $servicios->comisiones_ele;

        $totales->subtotal_apartados = $apertura->inicial_apartados - $gastos->gastos_apartados +
            $gastos->apartados_dia;

        $totales->subtotal_recargas_servicios = $apertura->inicial_recargas_servicios +
            $servicios->servicios_recargas_efe + $servicios->comisiones_efe - $gastos->gastos_recargas_servicios;

        $totales->subtotal_efe = round($totales->subtotal_efe, 2);
        $totales->subtotal_ele = round($totales->subtotal_ele, 2);
        $totales->subtotal_apartados = round($totales->subtotal_apartados, 2);
        $totales->subtotal_recargas_servicios = round($totales->subtotal_recargas_servicios, 2);

        return $totales;
    }
}
