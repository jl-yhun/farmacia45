<?php

namespace App\Classes\CierreCaja\Implementations;

use App\AperturaCaja;
use App\Classes\CierreCaja\Contracts\ITransferenciasCalculator;
use App\Classes\CierreCajaTransferencias;
use App\Enums\TipoTransferencia;
use App\Repositories\IAperturasCajaRepository;

class TransferenciasCalculator implements ITransferenciasCalculator
{
    private IAperturasCajaRepository $_aperturaCajaRepository;

    public function __construct(
        IAperturasCajaRepository $aperturaCajaRepository,
    ) {
        $this->_aperturaCajaRepository = $aperturaCajaRepository;
    }

    public function calculate(): CierreCajaTransferencias
    {
        $apertura = $this->_aperturaCajaRepository->getCurrent();
        $transferencias = new CierreCajaTransferencias();

        foreach ($apertura->transferencias as $transferencia) {
            if ($transferencia->tipo == TipoTransferencia::EfeEle->value) {
                $transferencias->efe_ele += $transferencia->monto;
            } else if ($transferencia->tipo == TipoTransferencia::EleEfe->value) {
                $transferencias->ele_efe += $transferencia->monto;
            }
        }
        return $transferencias;
    }
}
